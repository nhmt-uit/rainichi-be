<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.2/raphael-min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dmak@0.3.1/dist/dmak.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        #draw {
            height: 100vh;
            display:  flex;
            justify-content: center;
            align-items: center;
        }

        svg {
            position: fixed;
            top: 0;
            left: 0;
            height: 85%;
            width: auto!important;
        }

    </style>
</head>
<body>
<div id="draw"></div>

<script>
    var counter = 0
    var dmak = new Dmak("{{$kanji}}", {
        'element': "draw",
        loaded: function () {
        },
        // erased: function () {
        //     counter--
        //     if (counter == 0) {
        //         setTimeout(() => {
        //             dmak.render();
        //         }, 3000);
        
        //     }
        // },
        drew: function () {
            // counter++
            // if (counter == dmak.strokes.length) {
            //     setTimeout(() => {
            //         dmak.erase();
            //     }, 3000);
            // }
        },
        "uri": " https://api-prod.rainichi.com/kanji/",
        stroke: {order: {visible: true}, attr: {active: "#f37021"}},
        step: 0.02
    });

</script>
</body>
</html>
