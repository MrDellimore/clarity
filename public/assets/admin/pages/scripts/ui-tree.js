var UITree = function () {
//json cat structure
    var newsitedata = [{
        "id": "p1",
        "text": "Photo & Video",
        "children": [{
            "id": "p1c1",
            "text": "Cameras",
            "children": [{
                "id": "p1c1c1",
                "text": "Digital SLR"
            }, {
                "id": "p1c1c2",
                "text": "Point & Shoot"
            }, {
                "id": "p1c1c3",
                "text": "Camera Finder"

            }, {
                "id": "p1c1c4",
                "text": "Specialty"
            }, {
                "id": "p1c1c5",
                "text": "Build Your Own Kit"
            }]},
            {
                "id": "p1c2",
                "text": "Camera Accessories",
                "children": [{
                    "id": "p1c2c1",
                    "text": "Media & Storage"
                }, {
                    "id": "p2c2c2",
                    "text": "Straps"
                }, {
                    "id": "p1c2c3",
                    "text": "Lens Bands"
                }, {
                    "id": "p1c2c4",
                    "text": "Bags"
                }, {
                    "id": "p1c2c5",
                    "text": "Tripods"
                }, {
                    "id": "p1c2c6",
                    "text": "Filters",
                    "children": [{
                        "id": "p1c2c6c1",
                        "text": "Protective/UV/Skylight"
                    }, {
                        "id": "p1c2c6c2",
                        "text": "Polarizing"
                    }, {
                        "id": "p1c2c6c3",
                        "text": "Specialty",
                        "children": [{
                            "id": "p1c2c6c3c1",
                            "text": "Black & White Contrast Filters"
                        }, {
                            "id": "p1c2c6c3c2",
                            "text": "Color Conversion Filters"
                        }, {
                            "id": "p1c2c6c3c3",
                            "text": "Infrared Filters"
                        }, {
                            "id": "p1c2c6c3c4",
                            "text": "Special Effects Filters"
                        }]
                    }, {
                        "id": "p1c2c6c4",
                        "text": "Neutral Density Filters"
                    }, {
                        "id": "p1c2c6c5",
                        "text": "Filter Kits"
                    }]
                }]
            }, {
                "id": "p1c3",
                "text": "Lenses",
                "children": [{
                    "id": "p1c3c1",
                    "text": "Prime"
                }, {
                    "id": "p1c3c2",
                    "text": "Telephoto"
                }, {
                    "id": "p1c3c3",
                    "text": "Premium Glass"
                }, {
                    "id": "p1c3c4",
                    "text": "Lens Finder"
                }, {
                    "id": "p1c3c5",
                    "text": "Specialty"
                }, {
                    "id": "p1c3c6",
                    "text": "Build Your Own Kit"
                }]
            }, {
                "id": "p1c4",
                "text": "Video",
                "children": [{
                    "id": "p1c4c1",
                    "text": "Consumer Camcorders"
                }, {
                    "id": "p1c4c2",
                    "text": "Action Cameras"
                }, {
                    "id": "p1c4c3",
                    "text": "Aerial Photography"
                }, {
                    "id": "p1c4c4",
                    "text": "Trail Cameras"
                }]
            }, {
                "id": "p1c5",
                "text": "Video Accessories",
                "children": [{
                    "id": "p1c5c1",
                    "text": "Media & Storage"
                }]
//            }]
        }]
        }, {
        "id": "p2",
        "text": "Scopes and Optics",
        "children": [{
            "id": "p2c1",
            "text": "Binoculars"
        }, {
            "id": "p2c2",
            "text": "Telescopes",
            "children": [{
                "id": "p2c2c1",
                "text": "Accessories"
            }]
        }, {
            "id": "p2c3",
            "text": "Spotting Scopes",
            "children": [{
                "id": "p2c3c1",
                "text": "Accessories"
            }]
        }, {
            "id": "p2c4",
            "text": "Rifle Scopes",
            "children": [{
                "id": "p2c4c1",
                "text": "Accessories"
            }]
        }, {
            "id": "p2c5",
            "text": "Outdoor Gear",
            "children": [{
                "id": "p2c5c1",
                "text": "Accessories"
            }]
        }]
    }, {
        "id": "p3",
        "text": "Lighting & Studio",
        "children": [{
            "id": "p3c1",
            "text": "Lighting",
            "children": [{
                "id": "p3c1c1",
                "text": "Mono Lighting"
            }, {
                "id": "p3c1c2",
                "text": "Strobe Lighting"
            }, {
                "id": "p3c1c3",
                "text": "Continuous Lighting"
            }, {
                "id": "p3c1c4",
                "text": "On Camera Flash"
            }, {
                "id": "p3c1c5",
                "text": "Meters and Radio Slaves"
            }, {
                "id": "p3c1c6",
                "text": "Video Lighting"
            }]
        }, {
            "id": "p3c2",
            "text": "Light Modifiers",
            "children": [{
                "id": "p3c2c1",
                "text": "Soft Boxes"
            }, {
                "id": "p3c2c2",
                "text": "Umbrellas"
            }, {
                "id": "p3c2c3",
                "text": "Reflectors"
            }, {
                "id": "p3c2c4",
                "text": "Flash Modifiers"
            }]
        }, {
            "id": "p3c3",
            "text": "Studio",
            "children": [{
                "id": "p3c3c1",
                "text": "Backgrounds"
            }, {
                "id": "p3c3c2",
                "text": "Support"
            }, {
                "id": "p3c3c3",
                "text": "Tabletop Shooting"
            }]
        }, {
            "id": "p3c4",
            "text": "Accessories",
            "children": [{
                "id": "p3c4c1",
                "text": "Strands & Brackets"
            }]
        }]
    }, {
        "id": "p4",
        "text": "Electronics & Entertainment",
        "children": [{
            "id": "p4c1",
            "text": "Home Entertainment",
            "children": [{
                "id": "p4c1c1",
                "text": "TVs"
            }, {
                "id": "p4c1c2",
                "text": "Home Theater"
            }, {
                "id": "p4c1c3",
                "text": "Media Streaming"
            }, {
                "id": "p4c1c4",
                "text": "Projectors"
            }, {
                "id": "p4c1c5",
                "text": "Blu-Ray & DVD Players"
            }, {
                "id": "p4c1c6",
                "text": "Accessories",
                "children": [{
                    "id": "p4c1c6c1",
                    "text": "Cables"
                }, {
                    "id": "p4c1c6c2",
                    "text": "Bulbs & Lamps"
                }]
            }]
        }, {
            "id": "p4c2",
            "text": "Music & Audio",
            "children": [{
                "id": "p4c2c1",
                "text": "MP3 & Media Players"
            }, {
                "id": "p4c2c2",
                "text": "Headphones & Earbugs"
            }, {
                "id": "p4c2c3",
                "text": "Speakers & Docks"
            }, {
                "id": "p4c2c4",
                "text": "Accessories"
            }]
        }]
    }, {
        "id": "p5",
        "text": "Computers & Office",
        "children": [{
            "id": "p5c1",
            "text": "Home Office"
        }, {
            "id": "p5c2",
            "text": "Laptops",
            "children": [{
                "id": "p5c2c1",
                "text": "Chargers"
            }]
        }, {
            "id": "p5c3",
            "text": "Tablets"
        }, {
            "id": "p5c4",
            "text": "Monitors"
        }, {
            "id": "p5c5",
            "text": "Phones"
        }, {
            "id": "p5c6",
            "text": "Desktops & Servers"
        }, {
            "id": "p5c7",
            "text": "Storage"
        }, {
            "id": "p5c8",
            "text": "Printers"
        }, {
            "id": "p5c9",
            "text": "Cables & Accessories"
        }]
    }, {
        "id": "p6",
        "text": "Special Offers",
        "children": [{
            "id": "p6c1",
            "text": "Open Box"
        }, {
            "id": "p6c2",
            "text": "Manufacturer Refurbished"
        }, {
            "id": "p6c3",
            "text": "Rebates"
        }, {
            "id": "p6c4",
            "text": "Used"
        }]
    }];

    var oldsitedata = [{
        "id": "p1",
        "text": "Photo & Video",
        "children": [{
            "id": "p1c1",
            "text": "Cameras & Lenses",
            "children": [{
                "id": "p1c1c1",
                "text": "Digital SLR" },

                {"id": "p1c1c2",
                    "text": "Point & Shoot"},

                {"id": "p1c1c3",
                    "text": "Lenses"},

                {"id": "p1c1c4",
                    "text": "Film SLR"},

                {"id": "p1c1c5",
                    "text": "Filters"},

                {"id": "p1c1c6",
                    "text": "Sony Alpha Pre-Order"}]

        },
            {"id": "p1c2",
                "text": "Video",
                "children": [{
                    "id": "p1c2c1",
                    "text": "Video Cameras"},
                    {"id": "p1c2c2",
                        "text": "Recording Media"},
                    {"id": "p1c2c3",
                        "text": "Video Acessories"}]}
        ]},

        {"id":"p2",
            "text": "Binoculars & Scopes",
            "children": [{
                "id": "p2c1",
                "text": "Binoculars"},
                {"id": "p2c2",
                    "text": "Spotting Scopes"},
                {"id": "p2c3",
                    "text": "Telescopes & Microscopes"}]},

        {"id":"p3",
            "text": "Acessories"},

        {"id":"p4",
            "text": "Lighting & Studio"},

        {"id":"p5",
            "text": "Electronics & Computing"}
    ];
    /*[{
     "id": "p1",
     "text": "Photo & Video",
     "children": [{
     "id": "p1c1",
     "text": "Cameras & Lenses",
     "children": [{
     "id": "p1c1c1",
     "text": "Digital SLR" },

     {"id": "p1c1c2",
     "text": "Point & Shoot"},

     {"id": "p1c1c3",
     "text": "Lenses"},

     {"id": "p1c1c4",
     "text": "Film SLR"},

     {"id": "p1c1c5",
     "text": "Filters"},

     {"id": "p1c1c6",
     "text": "Sony Alpha Pre-Order"}]

     },
     {"id": "p1c2",
     "text": "Video",
     "children": [{
     "id": "p1c2c1",
     "text": "Video Cameras"},
     {"id": "p2c2",
     "text": "Recording Media"},
     {"id": "p1c2c2",
     "text": "Video Acessories"}]}
     ]},

     {"id":"p2",
     "text": "Binoculars & Scopes",
     "children": [{
     "id": "p2c1",
     "text": "Binoculars"},
     {"id": "p2c2",
     "text": "Spotting Scopes"},
     {"id": "p2c3",
     "text": "Telescopes & Microscopes"}]},

     {"id":"p3",
     "text": "Acessories"},

     {"id":"p4",
     "text": "Lighting & Studio"},

     {"id":"p5",
     "text": "Electronics & Computing"}

     /*"id":"p6",
     "text": "Gift Cards",

     "id":"p7",
     "text": "Electronics",

     "id":"p8",
     "text": "Office",

     "id":"p9",
     "text": "Free Bag and Memory Card",

     "id":"p10",
     "text": "Musical Instruments",

     "id":"p11",
     "text": "Home & Pets",

     "id":"p12",
     "text": "Apparel & Jewelry",

     "id":"p13",
     "text": "Baby & Kid",

     "id":"p14",
     "text": "Toys",

     "id":"p15",
     "text": "Sports & Outdoors",

     "id":"p16",
     "text": "Health & Wellness"*/
    var aSaveNewData = [{
        "id": "p1",
        "text": "Category",
        "children": [{
            "id": "p1c1",
            "text": "Home"},
            {
                "id": "p1c2",
                "text": "Electronics & Gadgets"
            }, {
                "id": "p1c3",
                "text": "Outdoors & Survival"
            }, {
                "id": "p1c4",
                "text": "Music",
                "children": [{
                    "id": "p1c4c1",
                    "text": "Creating"
                }, {
                    "id": "p1c4c2",
                    "text": "Enjoying"
                }]
            }, {
                "id": "p1c5",
                "text": "Office"
            }]}, {
        "id": "p2",
        "text": "Collections",
        "children": [{
            "id": "p2c1",
            "text": "Coffee Lover"
        }, {
            "id": "p2c2",
            "text": "Fashionista"
        }, {
            "id": "p2c3",
            "text": "Aspiring Gourmet"
        }, {
            "id": "p2c4",
            "text": "Explore the Outdoors"
        }, {
            "id": "p2c5",
            "text": "Go Go Gizmo"
        },{
            "id": "p2c6",
            "text": "Music is the Answer"
        }]}, {
        "id": "p3",
        "text": "Gift Ideas",
        "children": [{
            "id": "p3c1",
            "text": "Gift Finder"
        }, {
            "id": "p3c2",
            "text": "$1 - $50"
        }, {
            "id": "p3c3",
            "text": "$50 - $100"
        }, {
            "id": "p3c4",
            "text": "$100 - $250"
        },{
            "id": "p3c5",
            "text": "$250 or More"
        },{
            "id": "p3c6",
            "text": "Him"
        },{
            "id": "p3c7",
            "text": "Her"
        },{
            "id": "p3c8",
            "text": "Kids & Baby"
        }]}, {
        "id": "p4",
        "text": "Occasion",
        "children": [{
            "id": "p4c1",
            "text": "Birthday"
        }, {
            "id": "p4c2",
            "text": "Housewarming"
        },{
            "id": "p4c3",
            "text": "Wedding & Anniversary"
        },{
            "id": "p4c4",
            "text": "New Baby"
        },{
            "id": "p4c5",
            "text": "Graduation"
        },{
            "id": "p4c6",
            "text": "Congratulations"
        },{
            "id": "p4c7",
            "text": "Get Well"
        },{
            "id": "p4c8",
            "text": "Thank you"
        },{
            "id": "p4c9",
            "text": "Placeholder for Holiday"
        }
        ]}];

    var focustree = function () {
        $('#tree_2').jstree({
            'plugins': ["wholerow", "checkbox", "types"],
            'core': {
                "themes" : {
                    "responsive": false
                },    
                'data': oldsitedata},
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder icon-state-warning icon-lg"
                },
                "file" : {
                    "icon" : "fa fa-file icon-state-warning icon-lg"
                }
            }
        });

        //check if checked
        $('#tree_2').on('changed.jstree', function (e, data) {
            var wtf = data.selected.toString();
            wtf = wtf.split(",")

            var displayDivs  =[{"photo": false},{"bino": false}];
            for (i = 0; i < wtf.length; i++) {
                if(wtf[i].substring(0,2) == "p1"){
                    displayDivs['photo']=true;
                }
                if(wtf[i].substring(0,2) == "p2"){
                    displayDivs['bino']=true;
                }
            }
            //display
            if(displayDivs['photo'] == true){
                $('#photoAtt').css( "display", "block" );
                $('#photoAtt').css( "float", "top" );
            }
            else{
                $('#photoAtt').css( "display", "none" );
            }

            if(displayDivs['bino'] == true){
                $('#binocularsAtt').css( "display", "block" );
                $('#binocularsAtt').css( "float", "top" );
            }
            else{
                $('#binocularsAtt').css( "display", "none" );
            }

            console.log(wtf);
        });
    }

    var asavetree = function () {
        $('#asave').jstree({
            'plugins': ["wholerow", "checkbox", "types"],
            'core': {
                "themes" : {
                    "responsive": false
                },
                'data': oldsitedata},
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder icon-state-warning icon-lg"
                },
                "file" : {
                    "icon" : "fa fa-file icon-state-warning icon-lg"
                }
            }
        });
        //check if checked
        $('#asave').on('changed.jstree', function (e, data) {
            //for (i = 0; i < data.selected.length; i++) {
            //    console.log(data.selected[i]);
           // }

            var wtf = data.selected.toString();
            wtf = wtf.split(",")

            var displayDivs  =[{"photo": false},{"bino": false}];
            for (i = 0; i < wtf.length; i++) {
                if(wtf[i].substring(0,2) == "p1"){
                    displayDivs['photo']=true;
                }

                if(wtf[i].substring(0,2) == "p2"){
                    displayDivs['bino']=true;

                }
            }
            //display attributes
            if(displayDivs['photo'] == true){
                $('#photoAtt').css( "display", "block" );
                $('#photoAtt').css( "float", "top" );
            }
            else{
                $('#photoAtt').css( "display", "none" );
            }

            if(displayDivs['bino'] == true){
                $('#binocularsAtt').css( "display", "block" );
                $('#binocularsAtt').css( "float", "top" );
            }
            else{
                $('#binocularsAtt').css( "display", "none" );
            }

            console.log(wtf);
        });

    }
    var asavenewtree = function () {
        $('#newasave').jstree({
            'plugins': ["wholerow", "checkbox", "types"],
            'core': {
                "themes" : {
                    "responsive": false
                },
                'data': aSaveNewData},
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder icon-state-warning icon-lg"
                },
                "file" : {
                    "icon" : "fa fa-file icon-state-warning icon-lg"
                }
            }
        });
        //check if checked
        $('#newasave').on('changed.jstree', function (e, data) {
            //for (i = 0; i < data.selected.length; i++) {
            //    console.log(data.selected[i]);
           // }

            var wtf = data.selected.toString();
            wtf = wtf.split(",")

            var displayDivs  =[{"photo": false},{"bino": false}];
            for (i = 0; i < wtf.length; i++) {
                if(wtf[i].substring(0,2) == "p1"){
                    displayDivs['photo']=true;
                }

                if(wtf[i].substring(0,2) == "p2"){
                    displayDivs['bino']=true;

                }
            }
            //display attributes
            if(displayDivs['photo'] == true){
                $('#photoAtt').css( "display", "block" );
                $('#photoAtt').css( "float", "top" );
            }
            else{
                $('#photoAtt').css( "display", "none" );
            }

            if(displayDivs['bino'] == true){
                $('#binocularsAtt').css( "display", "block" );
                $('#binocularsAtt').css( "float", "top" );
            }
            else{
                $('#binocularsAtt').css( "display", "none" );
            }

            console.log(wtf);
        });

    }
        var focusnewtree = function () {
            $('#newfocus').jstree({
                'plugins': ["wholerow", "checkbox", "types"],
                'core': {
                    "themes" : {
                        "responsive": false
                    },
                    'data': newsitedata},

                "types" : {
                    "default" : {
                        "icon" : "fa fa-folder icon-state-warning icon-lg"
                    },
                    "file" : {
                        "icon" : "fa fa-file icon-state-warning icon-lg"
                    }
                }
            });

            //check if checked
            $('#newfocus').on('changed.jstree', function (e, data) {
                //for (i = 0; i < data.selected.length; i++) {
                //    console.log(data.selected[i]);
                // }

                var wtf = data.selected.toString();
                wtf = wtf.split(",")

                var displayDivs  =[{"photo": false},{"bino": false}];
                for (i = 0; i < wtf.length; i++) {
                    if(wtf[i].substring(0,2) == "p1"){
                        displayDivs['photo']=true;
                    }

                    if(wtf[i].substring(0,2) == "p2"){
                        displayDivs['bino']=true;

                    }
                }

                //display
                if(displayDivs['photo'] == true){
                    $('#photoAtt').css( "display", "block" );
                    $('#photoAtt').css( "float", "top" );
                }
                else{
                    $('#photoAtt').css( "display", "none" );
                }

                if(displayDivs['bino'] == true){
                    $('#binocularsAtt').css( "display", "block" );
                    $('#binocularsAtt').css( "float", "top" );
                }
                else{
                    $('#binocularsAtt').css( "display", "none" );
                }
/*
Create JSON/ with categories and corresponding attributes
if cat is checked loop though and display corresponding attributes.
Nest if statement to see if input is already listed.
 */
//                console.log(wtf);
            });
    }


    return {
        //main function to initiate the module
        init: function () {

            //handleSample1();
            focustree();
            asavetree();
            focusnewtree();
            asavenewtree();
            //contextualMenuSample();
            //ajaxTreeSample();

        }

    };

}();