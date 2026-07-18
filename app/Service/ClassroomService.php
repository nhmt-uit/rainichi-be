<?php

namespace App\Service;

use App\Mail\AddUserToClassroom;
use App\Mail\RemoveUserToClassroom;
use App\Mail\DeleteCourseClass;
use App\Mail\BuyCourseApproved;
use App\Models\Classroom;
use App\Models\Company;
use App\Models\CourseClass;
use App\Models\OrderPayment;
use App\Models\OrderPaymentCourse;
use App\Models\User;
use App\Notifications\BuyingDefaultClassroom;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClassroomService
{

    /**
     * @param $users
     * @param $classroom
     * @param bool $is_delete
     */
    public static function sendMailNotifyUserClass($users, $classroom, $is_delete = false)
    {
        if ($is_delete) {
            Mail::to($users)->send(new RemoveUserToClassroom($classroom));
        } else {
            Mail::to($users)->queue(new AddUserToClassroom($classroom));
        }
    }

    /**
     * @param $course_class
     * @param $is_delete
     */
    public static function sendMailApprovedToCompany($course_class, $is_delete = false)
    {
        $classroom = $course_class->classRoom ?? null;
        if ($classroom) {
            $course = $course_class->course ?? null;
            $users = User::query()
                ->join('user_company', 'user_company.user_id', '=', 'users.id')
                ->where('user_company.company_id', $classroom->company->id)
                ->where('user_company.is_active', 1)
                ->where('users.active', 1)
                ->where('users.type', User::LEADER)
                ->select('users.*')->get();
            Log::debug('>>>>>>>>>>>> User id will send email: ' . json_encode($users->pluck('id')));
            if ($is_delete) {
                Mail::to($users)->queue(new DeleteCourseClass($classroom, $course));
            } else {
                Mail::to($users)->queue(new BuyCourseApproved($classroom, $course));
            }
        }
    }

    /*
     * This function will:
     * + Create default company "Username + Date.now"
     * + Create default class with number the same with number_of_employee in course
     * + Create 1 record Oder for admin ( Should be add currency type, classroom_id )
     * + Send mail notify to User
     * + Update profile current user to User::LEADER
     * */
    /**
     * @param $orderPayment
     * @return void
     */
    public static function createDefaultClassroom($orderPayment)
    {
        try {
            $orderCourse = $orderPayment->paymentCourse;
            $classRoom = null;
            //If class is exist, we will get it
            if ($orderCourse->classroom_id) {
                $classRoom = Classroom::query()->find($orderCourse->classroom_id);
            }
            if ($orderCourse->course_id || $orderCourse->test_id) {
                $user = User::query()->find($orderPayment->user_id);
                if (empty($classRoom) && ($user->isLeader() || $user->isUser())) {
                    $company = Company::getCompanyByUser($user->id);
                    if (empty($company)) {
                        $company = self::createCompanyDefault($user);
                    }
                    $classRoom = self::createClassroomDefault($company, $user->id, $orderCourse->num_of_employee);
                } else {
                    $company = $classRoom->company ?? null;
                }

                //create classroom Default
                if ($company && $classRoom) {
                    self::addCourseClass($classRoom->id, $user->id, $orderCourse, $orderPayment);
                    if ($orderCourse) {
                        $orderCourse->updateClassInfo($classRoom->id, $classRoom->name);
                    }
                    $is_course = empty($orderCourse->course_id) ? false : true;
                    //this case will handle send notify email to user & admins
                    if ($user->isAdmin()) {
                        Log::debug('****** Current user is Admin so will get user company');
                        foreach ($company->users()->get() as $user) {
                            $user->notify(new BuyingDefaultClassroom($classRoom, $is_course));
                        }
                    } else {
                        Log::debug('****** Current user is Company admin so will sent now');
                        $user->notify(new BuyingDefaultClassroom($classRoom, $is_course));
                    }
                    Log::debug('Create classroom && add course successfully. Please contact admin and login to admin page');
                }

            }
        } catch (Exception $exception) {
            Log::error('Something went wrong when user try to create default class:' . $exception->getMessage());
        }
    }

    /**
     * @param $orderPayment
     * @param $is_approved
     */
    public static function rejectOrApprovedCourseClass($orderPayment, $is_approved)
    {
        $paymentCourse = $orderPayment->paymentCourse;
        $course_class = CourseClass::query()->where('classroom_id', $paymentCourse->classroom_id)
            ->where('order_payment_id', $orderPayment->id)
            ->where('course_id', $paymentCourse->course_id)->first();
        $classRoom = Classroom::query()->where('id', $paymentCourse->classroom_id)->first();
        $company = $classRoom->company;
        if ($is_approved) {
            $user_ids = $company->users()->select('users.*')->pluck('users.id');
            if ($user_ids) {
                User::query()->whereIn('id', $user_ids)->update(['type' => User::LEADER]);
            }
            if ($classRoom) {
                $classRoom->updateApproved(true, $orderPayment->updated_by);
            }
            if ($course_class) {
                $course_class->updateApproved(true, $orderPayment->updated_by);
            }
            $company->balance += $orderPayment->amount;
            self::sendMailApprovedToCompany($course_class);
        } else {
            //how about if reject the last ??
            if ($course_class) {
                $course_class->updateApproved(false, $orderPayment->updated_by);
            }
            $company->balance > $orderPayment->amount ? $company->balance -= $orderPayment->amount : $company->balance = 0;
        }
        $company->save();
    }

    /**
     * @param $user
     * @return mixed
     */
    public static function createCompanyDefault($user)
    {
        //Create default Company
        $dataCompany = [
            'email' => $user->email,
            'phone' => $user->phone,
            'num_of_employee' => null,
            'created_by' => $user->id
        ];
        $company = Company::firstOrCreate($dataCompany);
        foreach (["vi", "en"] as $language) {
            $company->translateOrNew($language)->name = "Doanh nghiệp " . $user->name;
            $company->translateOrNew($language)->career = "";
            $company->translateOrNew($language)->content = "";
        }
        $company->save();
        #assignee current user to company
        $company->users()->sync($user);
        return $company;
    }

    /**
     * @param $company
     * @param $user_id
     * @param $num_of_employee
     * @return mixed
     */
    public static function createClassroomDefault($company, $user_id, $num_of_employee)
    {
        $dataClass = [
            'name' => 'Lớp học mua từ Cửa hàng-' . $company->name,
            'num_of_employee' => $num_of_employee,
            'created_by' => $user_id,
            'company_id' => $company->id,
            'credits' => 0,
            'is_active' => 0
        ];
        return Classroom::create($dataClass);
    }

    /**
     * @param $class_id
     * @param $user_id
     * @param $payment
     * @param $orderCourse
     * @return mixed
     */
    public static function addCourseClass($class_id, $user_id, $orderCourse, $payment)
    {
        $dataCourse = [
            'classroom_id' => $class_id,
            'test_id' => $orderCourse->test_id ?? null,
            'course_id' => $orderCourse->course_id ?? null,
            'created_by' => $user_id,
            'duration' => $orderCourse->duration,
            'buying_credits' => $payment->final_amount,
            'reward_credits' => 0,
            'num_of_employee' => $orderCourse->num_of_employee,
            'type' => $orderCourse->classroom_id ? CourseClass::ADD_BY_DEFAULT : CourseClass::ADD_BY_ADMIN,
            'order_payment_id' => $payment->id
        ];
        return CourseClass::create($dataCourse);
    }

    /**
     * @param $courseClass
     * @return bool
     */
    public static function isApproveOrderPayment($courseClass)
    {
        $orderCourse = OrderPaymentCourse::query()->where('classroom_id', $courseClass->classroom_id)
            ->where('order_payment_id', $courseClass->order_payment_id)
            ->where('course_id', $courseClass->course_id)
            ->where('test_id', $courseClass->test_id)->first();
        if ($orderCourse) {
            return $orderCourse->orderPayment->payment_status <> OrderPayment::DONE ? false : true;
        }
        return false;
    }

}
