var UITree = function () {


    var newsitedata1 = [{
        "id": "p1",
        "state":{"seleced": true},
        "text": "Photo & Videos",
        "children": [{
            "id": "p1c1",
            "text": "Cameras",
            "children": [{
                "id": "p1c1c1",
                "text": "Digital SLR"
                //"state":{"seleced": true}
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

    var testSite = [{"id":"3","parent":"#", "text":"Root Catalog",state: {"opened": true}},{"id":"41","parent":"3","text":"Photo & Video"},{"id":"42","parent":"41","text":"Cameras & Lenses"},{"id":"43","parent":"41","text":"Video"},{"id":"44","parent":"3","text":"Binoculars & Scopes"},{"id":"45","parent":"3","text":"Accessories"},{"id":"46","parent":"3","text":"Lighting & Studio"},{"id":"47","parent":"3","text":"Electronics & Computing"},{"id":"48","parent":"42","text":"Film SLR"},{"id":"49","parent":"42","text":"Filters"},{"id":"50","parent":"43","text":"Recording Media"},{"id":"51","parent":"43","text":"Video Accessories"},{"id":"52","parent":"44","text":"Binoculars"},{"id":"53","parent":"44","text":"Spotting Scopes"},{"id":"54","parent":"44","text":"Telescopes & Microscopes"},{"id":"55","parent":"45","text":"Tripods"},{"id":"56","parent":"45","text":"Camera Bags & Cases"},{"id":"57","parent":"45","text":"Batteries & Chargers"},{"id":"58","parent":"45","text":"Accessories"},{"id":"59","parent":"45","text":"Memory Cards"},{"id":"60","parent":"46","text":"Camera Flash"},{"id":"61","parent":"46","text":"Studio Lighting"},{"id":"62","parent":"46","text":"Backgrounds & other studio equipment"},{"id":"63","parent":"46","text":"Flashlights"},{"id":"64","parent":"47","text":"Computers"},{"id":"65","parent":"47","text":"TV's & Entertainment"},{"id":"66","parent":"47","text":"Music & Audio"},{"id":"67","parent":"64","text":"Storage"},{"id":"68","parent":"64","text":"Tablets"},{"id":"69","parent":"64","text":"Printers"},{"id":"70","parent":"64","text":"Business systems"},{"id":"71","parent":"64","text":"Cables & Accessories"},{"id":"72","parent":"65","text":"TV"},{"id":"73","parent":"65","text":"Cables & Accessories"},{"id":"74","parent":"65","text":"Home Theater"},{"id":"75","parent":"66","text":"Media Players"},{"id":"76","parent":"66","text":"Headphones & Earbuds"},{"id":"77","parent":"66","text":"Radios & Players"},{"id":"78","parent":"66","text":"Vehicle Audio"},{"id":"79","parent":"66","text":"Phones"},{"id":"80","parent":"66","text":"Accessories"},{"id":"81","parent":"42","text":"Digital SLR"},{"id":"82","parent":"42","text":"Point & Shoot"},{"id":"83","parent":"42","text":"Lenses"},{"id":"84","parent":"43","text":"Video Cameras"},{"id":"85","parent":"3","text":"Gift Cards"},{"id":"86","parent":"3","text":"Electronics"},{"id":"87","parent":"86","text":"Accessories"},{"id":"88","parent":"86","text":"Cell Phones"},{"id":"89","parent":"86","text":"Cell Phone Accessories"},{"id":"90","parent":"86","text":"Home Audio & Theater"},{"id":"91","parent":"86","text":"GPS & Navigations"},{"id":"92","parent":"86","text":"Ipad, Tablets & E-readers"},{"id":"93","parent":"86","text":"Ipods & Mp3 Players"},{"id":"94","parent":"86","text":"DVD"},{"id":"95","parent":"86","text":"Video Games & Consoles"},{"id":"96","parent":"3","text":"Office"},{"id":"97","parent":"96","text":"Office Supplies"},{"id":"98","parent":"96","text":"Phones & Accessories"},{"id":"99","parent":"96","text":"Office Technology"},{"id":"100","parent":"3","text":"Musical Instruments"},{"id":"101","parent":"100","text":"Guitars"},{"id":"102","parent":"100","text":"Basses"},{"id":"103","parent":"100","text":"Drums"},{"id":"104","parent":"100","text":"Keyboards"},{"id":"105","parent":"100","text":"Recording & Mixers"},{"id":"106","parent":"100","text":"Accessories"},{"id":"107","parent":"3","text":"Home & Pets"},{"id":"108","parent":"107","text":"Appliances"},{"id":"109","parent":"107","text":"Bedding"},{"id":"110","parent":"107","text":"Kitchen & Dining"},{"id":"111","parent":"107","text":"Luggage"},{"id":"112","parent":"107","text":"Pets"},{"id":"113","parent":"107","text":"Vacuums & Floor Care"},{"id":"114","parent":"107","text":"Patio & Garden"},{"id":"115","parent":"114","text":"Grills & Cooking"},{"id":"116","parent":"114","text":"Outdoor Play"},{"id":"117","parent":"107","text":"Home Improvement"},{"id":"118","parent":"117","text":"Home\/Office Security"},{"id":"119","parent":"117","text":"Home Safety"},{"id":"120","parent":"117","text":"Pest Control"},{"id":"121","parent":"3","text":"Apparel & Jewelry"},{"id":"122","parent":"121","text":"Watches"},{"id":"123","parent":"121","text":"Bag & Luggage"},{"id":"124","parent":"121","text":"Accessories"},{"id":"125","parent":"121","text":"Belts"},{"id":"126","parent":"121","text":"Wallets"},{"id":"127","parent":"3","text":"Baby & Kid"},{"id":"128","parent":"127","text":"Activities & Toys"},{"id":"129","parent":"127","text":"Car Seats"},{"id":"130","parent":"127","text":"Diaper"},{"id":"131","parent":"127","text":"Baby Gear"},{"id":"132","parent":"127","text":"Health & Safety"},{"id":"133","parent":"127","text":"Strollers"},{"id":"134","parent":"127","text":"Toddler"},{"id":"135","parent":"3","text":"Toys"},{"id":"136","parent":"135","text":"Action Figures"},{"id":"137","parent":"135","text":"Bikes & Riding Toys"},{"id":"138","parent":"135","text":"Building Set & Blocks"},{"id":"139","parent":"135","text":"Dolls"},{"id":"140","parent":"135","text":"Games & Puzzles"},{"id":"141","parent":"135","text":"Pre-School"},{"id":"142","parent":"135","text":"Pretend Play & Arts & Crafts"},{"id":"143","parent":"135","text":"Outdoor Play"},{"id":"144","parent":"135","text":"Video Games"},{"id":"145","parent":"144","text":"Consoles"},{"id":"146","parent":"144","text":"Video Games"},{"id":"147","parent":"144","text":"Accessories"},{"id":"148","parent":"3","text":"Sports & Outdoors"},{"id":"149","parent":"148","text":"Exercise & Fitness"},{"id":"150","parent":"148","text":"Outdoor & Recreation"},{"id":"151","parent":"3","text":"Health & Wellness"},{"id":"152","parent":"151","text":"Health"},{"id":"153","parent":"151","text":"Beauty"},{"id":"154","parent":"151","text":"Medical Supplies"},{"id":"156","parent":"86","text":"Audio Components"},{"id":"157","parent":"156","text":"Headphones"},{"id":"159","parent":"42","text":"Sony Alpha Pre-Order"},{"id":"160","parent":"3","text":"Specials"},{"id":"162","parent":"160","text":"Labor Day 2013 Deals"},{"id":"166","parent":"3","text":"Deals"},{"id":"167","parent":"3","text":"catalog"},{"id":"168","parent":"167","text":"Canon"},{"id":"169","parent":"167","text":"Nikon"},{"id":"170","parent":"167","text":"Sony"},{"id":"171","parent":"167","text":"Social"},{"id":"172","parent":"167","text":"Samsung"},{"id":"173","parent":"167","text":"Olympus"},{"id":"175","parent":"3","text":"Government"},{"id":"177","parent":"3","text":"Focus"},{"id":"178","parent":"3","text":"Asavings"},{"id":"179","parent":"178","text":"Black Friday"},{"id":"180","parent":"177","text":"Black Friday"},{"id":"181","parent":"177","text":"Cyber Monday"},{"id":"182","parent":"178","text":"Cyber Monday"},{"id":"183","parent":"177","text":"Holiday"},{"id":"184","parent":"178","text":"Holiday"},{"id":"185","parent":"3","text":"Knox"},{"id":"186","parent":"3","text":"Apple Accessories"},{"id":"187","parent":"3","text":"Fuji"},{"id":"188","parent":"3","text":"Free Two Day Shipping"},{"id":"189","parent":"3","text":"Special Price"},{"id":"190","parent":"3","text":"Rangefinder Deals"},{"id":"191","parent":"3","text":"Sony A7"},{"id":"192","parent":"3","text":"Victorinox "},{"id":"193","parent":"3","text":"Marumi Filters"},{"id":"194","parent":"3","text":"Hard Drives"},{"id":"195","parent":"3","text":"Cuisinart"},{"id":"196","parent":"3","text":"Sony TradeUp to a7 Series Event"},{"id":"198","parent":"3","text":"CES"},{"id":"199","parent":"177","text":"email"},{"id":"200","parent":"3","text":"Adobe Lightroom 5 with Purchase"},{"id":"201","parent":"3","text":"Lensbaby"},{"id":"202","parent":"177","text":"Celestron Cosmos"},{"id":"203","parent":"3","text":"Vtech"},{"id":"204","parent":"3","text":"Mothers Day"},{"id":"207","parent":"3","text":"Bushnell Trophy Bucks"},{"id":"210","parent":"3","text":"Sony Pre-Orders"},{"id":"211","parent":"3","text":"Free Bag and Memory Card"},{"id":"212","parent":"3","text":"Fathers Day"},{"id":"213","parent":"3","text":"Free Music Book"},{"id":"214","parent":"3","text":"Bushnell Bone Collector Savings Event"},{"id":"215","parent":"178","text":"email"},{"id":"216","parent":"3","text":"Free Frozen Song Book"},{"id":"217","parent":"3","text":"New Nikon D810"},{"id":"218","parent":"3","text":"The Impossible Project"}];
    var oldsitedata = testSite;
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
                'data': testSite},
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
/*
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

    */
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

        var url = '/form/categoryload';
        $.ajax({
            url: url,
            dataType: "json"})
            .done(function( data ) {
                //console.log(data);
                //newsitedata = data;
                $('#newfocus').jstree({
                    'plugins': ["wholerow", "checkbox", "types"],
                    'core': {
                        "themes" : {
                            "responsive": false
                        },
                        'data': testSite},

                    "types" : {
                        "default" : {
                            "icon" : "fa fa-folder icon-state-warning icon-lg"
                        },
                        "file" : {
                            "icon" : "fa fa-file icon-state-warning icon-lg"
                        }
                    }
                });
            });


        //$('#categoriesForm').each();
        //input.val()
        $('#newfocus').jstree('select_node', 'p1c2');
        console.log( $('#newfocus').jstree('is_parent', 'p1c1'));

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
            //asavetree();
            focusnewtree();
            asavenewtree();
            //contextualMenuSample();
            //ajaxTreeSample();

        }

    };

}();