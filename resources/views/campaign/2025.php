<!DOCTYPE html>
<html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
           
        <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets_landing_page/')?>img/Tisya_Symbol_Green.png" />
		<title>tisyastays | Live & Lounge | Luxury Holiday Homes</title>                 
		<meta name="description" content="A contemporary feel with traditional luxuries is what Tisya Stays aims for. A promise to all the travelers to have the most authentic local experiences where they won't be worried about bringing their own identities." />
		<meta name="keywords" content="tisyastays | Live & Lounge | Luxury Holiday Homes" />
		<meta property="og:url"         content="https://www.tisyastays.com/" />
		<meta property="og:type"        content="website" />
		<meta property="og:site_name"   content="tisyastays | Live & Lounge | Luxury Holiday Homes">
		<meta property="og:title" content="tisyastays | Live & Lounge | Luxury Holiday Homes" />
		<meta property="og:description" content="A contemporary feel with traditional luxuries is what Tisya Stays aims for. A promise to all the travelers to have the most authentic local experiences where they won't be worried about bringing their own identities." />
		<meta property="og:image"  itemprop="image"  content="https://www.tisyastays.com/assest_front/ogtag.jpg" />
		<meta property="og:image:secure_url" content="https://www.tisyastays.com/assest_front/ogtag.jpg">
		<meta property="og:image:width" content="400">
		<meta property="og:image:height" content="400">
		<meta property="og:image:alt" content="tisyastays">
		<meta property="og:image:type" content="image/jpeg">                              

        <!-- Icon css link -->
        <link href="<?php echo base_url('assets_landing_page/')?>css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/stroke-icon/style.css" rel="stylesheet">
        <!-- Bootstrap --> 
        <link href="<?php echo base_url('assets_landing_page/')?>css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Rev slider css -->   
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/revolution/css/settings.css" rel="stylesheet">
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/revolution/css/layers.css" rel="stylesheet">
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/revolution/css/navigation.css" rel="stylesheet">
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/animate-css/animate.css" rel="stylesheet">
        
        <!-- Extra plugin css -->
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/magnify-popup/magnific-popup.css" rel="stylesheet">
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/owl-carousel/owl.carousel.min.css" rel="stylesheet">
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/bootstrap-selector/bootstrap-select.css" rel="stylesheet">
        <link href="<?php echo base_url('assets_landing_page/')?>vendors/lightbox/simpleLightbox.css" rel="stylesheet">
        
        <link href="<?php echo base_url('assets_landing_page/')?>css/style.css" rel="stylesheet">
        <link href="<?php echo base_url('assets_landing_page/')?>css/responsive.css" rel="stylesheet">
		
		        
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Tenor+Sans&display=swap" rel="stylesheet">
		                  


       <style>
        .iframe-container{
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* Ratio 16:9 ( 100%/16*9 = 56.25% ) */
        }
        .iframe-container > *{
            display: block;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
		
                
        .float{
            position:fixed;
            width:60px;
            height:60px;
            bottom:40px;
            right:40px;
            background-color:#25d366;
            color:#FFF;
            border-radius:50px;
            text-align:center;
            box-shadow: 2px 2px 3px #999;
            z-index: 9999;
        }
           
        .my-float{
            margin-top: 15px;
            font-size: 30px;
        }
        .float-call{
            position:fixed;
            width:60px;
            height:60px;
            bottom:111px;                                
            right:40px;
            background-color:#1c7ce0;
            color:#FFF;
            border-radius:50px;
            text-align:center;
            box-shadow: 2px 2px 3px #999;
            z-index: 9999;
        }

        #exampleModal .modal-content {
            padding: 15px 0 0px 0;
        }
        .modal-header {
            border-bottom: 0px;
            padding: 0px;
        }
        .modal-header img {
            width: 200px;
            text-align: center;
            margin: auto;
            padding: 15px 0 0 0;
        }             
        #exampleModal .close {
            float: right;
            font-size: 21px;
            font-weight: 700;
            line-height: 1;
            color: #00423c;
            text-shadow: 0 1px 0 #fff;
            top: 5px;
            position: absolute;
            right: 5px;
        }

        .modal-body {
            position: relative;
            padding: 0px 15px;
        }

        .modal-body .form-control {
            border-radius: 20px;
            padding: 12px 12px;
            height: auto;
            border: solid 1px #dedede;
            margin-bottom: 15px;
            box-shadow: none;
        }
          
        .modal-body input[type=submit] {
            padding: 12px 12px;
            background: #00423c;
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            border: none;
            width: 100%;
            border-radius: 20px;
        }

        .modal .term-condition {
            margin-top: 10px;
            margin-bottom: 15px;
            color: #00423c;
            font-size: 12px;
            display: inline-block;
        }

        .modal-footer {
            border-top: 0px;
            text-align: center;
            margin: 25px 0 0 0;
            padding: 10px 10px;
            background: #00423c;
        }
        .text-center {
            text-align: center;
        }       
        .modal-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            color: #00423c;
            margin: 0px 0 20px 0;
            position: relative;
            padding: 0px 0 10px 0;
        }                                             
        .modal-dialog {
            width: 480px;
        }        
                               
        .modal h6 span a {
            color: #ffffff;                     
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 20px;
        }                         

        @media (max-width: 767px) {
            .modal-header img {
            }
            .modal-title {
                font-size: 13px;
            }
            .modal-dialog {
                width: auto;
            }
        }       



       </style>
   
    </head>
    <body>

        <!--================Header Area =================-->
        <header class="main_header_area tp_header_area">
            <div class="header_top_logo">
                <div class="container">
                    <div class="header_top_l_inner">
                        <div class="h_left_text">
                            <a href="tel:8799915100"><img src="<?php echo base_url('assets_landing_page/')?>img/icon/phone-icon.png" alt="">87999 15100</a>
                        </div>          
                        <div class="h_middle_text">
                            <a href="javascript:void(0)"><img src="<?php echo base_url('assets_landing_page/')?>img/tisyastays-logo-white.png" alt=""></a>
                        </div>               
                        <div class="h_right_text">
                            <a class="book_now_btn" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">Book now</a>
                        </div>
                    </div>
                </div>
            </div>                                         
            <div class="middle_menu_area">
                <nav class="navbar navbar-default">
                    <div class="container">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="#">
                                <img src="<?php echo base_url('assets_landing_page/')?>img/tisyastays-logo-white.png" alt="">
                                <img src="<?php echo base_url('assets_landing_page/')?>img/logo-sticky.png" alt="">
                            </a>
                        </div>

                   
                    </div><!-- /.container-fluid -->
                </nav>
            </div>
        </header>
        <!--================Header Area =================-->
        
        
        <!--================Slider Area =================-->
        <section class="main_slider_area">
            <div id="main_slider3" class="rev_slider" data-version="5.3.1.6">
                <ul>
                    <li data-index="rs-1587" data-transition="fade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off"  data-easein="default" data-easeout="default" data-masterspeed="300"  data-thumb="<?php echo base_url('assets_landing_page/')?>img/home-slider/slider-5.jpg"  data-rotate="0"  data-saveperformance="off"  data-title="Creative" data-param1="01" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
                    <!-- MAIN IMAGE -->
                    <img src="<?php echo base_url('assets_landing_page/')?>img/home-slider/slider-5.jpg"  alt="" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="5" class="rev-slidebg" data-no-retina>
     <!-- LAYER NR. 1 -->                         
                        <div class="slider_text_box">
                            <div class="tp-caption tp-resizeme first_text" 
                            id="slide-1586-layer-1" 
                            data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                            data-y="['top','top','top','top']" data-voffset="['220','220','220','220','130']" 
                            data-fontsize="['55','55','55','40','25']"
                            data-lineheight="['59','59','59','50','35']"
                            data-width="['550','550','550','550','300']"
                            data-height="none"
                            data-whitespace="normal"
                            data-type="text" 
                            data-responsive_offset="on" 
                            data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[-100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:0px;s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]"
                            data-textAlign="['left','left','left','left']">BOOK YOUR LUXURY VILLA STAY IN GOA</div>
                                       
							<div class="tp-caption tp-resizeme secand_text" 
                                id="slide-1593-layer-2" 
                                data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                                data-y="['top','top','top','top']" data-voffset="['435','435','435','340','225']"  
                                data-fontsize="['18','18','18','18','16']"
                                data-lineheight="['26','26','26','26']"
                                data-width="['560','560','560','550','300']"
                                data-height="none"
                                data-whitespace="normal"
                                data-type="text" 
                                data-responsive_offset="on"
                                data-transform_idle="o:1;"
                                data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:[100%];s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]">Unforgettable Experiences Await
                            </div>
                            
                            <div class="tp-caption tp-resizeme slider_button" 
                                id="slide-1594-layer-3" 
                                data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                                data-y="['top','top','top','top']" data-voffset="['525','525','525','425','350']" 
                                data-fontsize="['14','14','14','14']"
                                data-lineheight="['46','46','46','46']"
                                data-width="none"
                                data-height="none"
                                data-whitespace="nowrap"
                                data-type="text" 
                                data-responsive_offset="on" 
                                data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:[100%];s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]">
                                
								<a  class="slider_btn" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">Enquire Now</a>
								 
                            </div> 
                                         
                            
                                
                        </div>                 
                   
				   </li>
                    <li data-index="rs-1588" data-transition="fade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off"  data-easein="default" data-easeout="default" data-masterspeed="300"  data-thumb="<?php echo base_url('assets_landing_page/')?>img/home-slider/slider-6.jpg"  data-rotate="0"  data-saveperformance="off"  data-title="Creative" data-param1="01" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
                    <!-- MAIN IMAGE -->
                    <img src="<?php echo base_url('assets_landing_page/')?>img/home-slider/slider-6.jpg"  alt="" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="5" class="rev-slidebg" data-no-retina>
                    <!-- LAYERS -->
                        <!-- LAYERS -->
						<div class="slider_text_box">
                            <div class="tp-caption tp-resizeme first_text" 
                            id="slide-1586-layer-1" 
                            data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                            data-y="['top','top','top','top']" data-voffset="['220','220','220','220','130']" 
                            data-fontsize="['55','55','55','40','25']"
                            data-lineheight="['59','59','59','50','35']"  
                            data-width="['550','550','550','550','300']"
                            data-height="none"
                            data-whitespace="normal"
                            data-type="text" 
                            data-responsive_offset="on" 
                            data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[-100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:0px;s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]"
                            data-textAlign="['left','left','left','left']">BOOK YOUR LUXURY VILLA STAY IN GOA</div>
                                      
                                

							<div class="tp-caption tp-resizeme secand_text" 
                                id="slide-1593-layer-2" 
                                data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                                data-y="['top','top','top','top']" data-voffset="['435','435','435','340','225']"  
                                data-fontsize="['18','18','18','18','16']"
                                data-lineheight="['26','26','26','26']"
                                data-width="['560','560','560','550','300']"
                                data-height="none"
                                data-whitespace="normal"
                                data-type="text" 
                                data-responsive_offset="on"
                                data-transform_idle="o:1;"
                                data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:[100%];s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]">Unforgettable Experiences Await
                            </div>
                            
                            <div class="tp-caption tp-resizeme slider_button" 
                                id="slide-1594-layer-3" 
                                data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                                data-y="['top','top','top','top']" data-voffset="['525','525','525','425','350']" 
                                data-fontsize="['14','14','14','14']"
                                data-lineheight="['46','46','46','46']"
                                data-width="none"
                                data-height="none"
                                data-whitespace="nowrap"
                                data-type="text" 
                                data-responsive_offset="on" 
                                data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:[100%];s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]">
                               <a  class="slider_btn" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">Enquire Now</a>
                            </div> 
                           
						   </div>         						
                   
				   

                        <!-- LAYER NR. 1 -->
                    </li>
                    <li data-index="rs-1589" data-transition="fade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off"  data-easein="default" data-easeout="default" data-masterspeed="300"  data-thumb="<?php echo base_url('assets_landing_page/')?>img/home-slider/slider-7.jpg"  data-rotate="0"  data-saveperformance="off"  data-title="Creative" data-param1="01" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
                    <!-- MAIN IMAGE -->
                    <img src="<?php echo base_url('assets_landing_page/')?>img/home-slider/slider-7.jpg"  alt="" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="5" class="rev-slidebg" data-no-retina>
                        <!-- LAYER NR. 1 -->
						
						<div class="slider_text_box">
                            <div class="tp-caption tp-resizeme first_text" 
                            id="slide-1586-layer-1" 
                            data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                            data-y="['top','top','top','top']" data-voffset="['220','220','220','220','130']" 
                            data-fontsize="['55','55','55','40','25']"  
                            data-lineheight="['59','59','59','50','35']"
                            data-width="['550','550','550','550','300']"
                            data-height="none"
                            data-whitespace="normal"
                            data-type="text" 
                            data-responsive_offset="on" 
                            data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[-100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:0px;s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]"
                            data-textAlign="['left','left','left','left']">BOOK YOUR LUXURY VILLA STAY IN GOA</div>
                                  
							<div class="tp-caption tp-resizeme secand_text" 
                                id="slide-1593-layer-2" 
                                data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                                data-y="['top','top','top','top']" data-voffset="['435','435','435','340','225']"  
                                data-fontsize="['18','18','18','18','16']"
                                data-lineheight="['26','26','26','26']"
                                data-width="['560','560','560','550','300']"
                                data-height="none"
                                data-whitespace="normal"
                                data-type="text" 
                                data-responsive_offset="on"
                                data-transform_idle="o:1;"
                                data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:[100%];s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]">Unforgettable Experiences Await
                            </div>                                     
                            
                            <div class="tp-caption tp-resizeme slider_button" 
                                id="slide-1594-layer-3" 
                                data-x="['left','left','left','15','0']" data-hoffset="['0','0','0','0']" 
                                data-y="['top','top','top','top']" data-voffset="['525','525','525','425','350']" 
                                data-fontsize="['14','14','14','14']"
                                data-lineheight="['46','46','46','46']"
                                data-width="none"
                                data-height="none"
                                data-whitespace="nowrap"
                                data-type="text" 
                                data-responsive_offset="on" 
                                data-frames="[{&quot;delay&quot;:10,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;0&quot;,&quot;from&quot;:&quot;y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;&quot;,&quot;mask&quot;:&quot;x:0px;y:[100%];s:inherit;e:inherit;&quot;,&quot;to&quot;:&quot;o:1;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;},{&quot;delay&quot;:&quot;wait&quot;,&quot;speed&quot;:1500,&quot;frame&quot;:&quot;999&quot;,&quot;to&quot;:&quot;y:[175%];&quot;,&quot;mask&quot;:&quot;x:inherit;y:inherit;s:inherit;e:inherit;&quot;,&quot;ease&quot;:&quot;Power2.easeInOut&quot;}]">
                                <a  class="slider_btn" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">Enquire Now</a>
                            </div>                     
                                      								  
                           
                            
                                                             
                        </div>    
						
                                
                    </li>
                </ul>
            </div>                  
            <div class="book_room_area" id="book_room_area">
                <div class="container">
                    <div class="book_room_box">
                        <div class="book_table_item">
                            <h3>Book Now</h3>
                        </div>
						<form  action="" method="POST">
                        <div class="book_table_item">
                            <div class="input-append">
                                <input type="text" value="" name="enqname" id="enqname" required  placeholder="Name">                        
                            </div>
                        </div>
						
						 <div class="book_table_item">
                            <div class="input-append">
                                <input type="number" value="" class="mobile-valid" name="enqcontactnum" id="enqcontactnum" required  placeholder="Mobile">                        
                            </div>     
                          </div>                     
						  
						   <div class="book_table_item">
                            <div class="input-append">
                                <input type="email" value="" name="enqemail" id="enqemail" required  placeholder="Email">                        
                            </div>
                          </div>
                                 
						                                                                                       
					                                                                
                                                           
                        <div class="book_table_item">
                            <button type="submit" class="book_now_btn_black"  >Submit</button>
                        </div>  
						</form>           
                    </div>                         
                </div>                
            </div>                       
        </section>              
        <!--================End Slider Area =================-->
		<section class="book_table_area">
            <div class="container">       
                <div class="book_table_inner row m0">
				<form  action="" method="POST">
                        <div class="book_table_item">
                            <div class="input-append">
                                <input type="text" value="" name="enqname" id="enqname" required  placeholder="Name">                        
                            </div>
                        </div>
						
						 <div class="book_table_item">
                            <div class="input-append">
                                <input type="number" value="" class="mobile-valid" name="enqcontactnum" id="enqcontactnum" required  placeholder="Mobile">                        
                            </div>     
                          </div>                     
						  
						   <div class="book_table_item">
                            <div class="input-append">
                                <input type="email" value="" name="enqemail" id="enqemail" required  placeholder="Email">                        
                            </div>
                          </div>
                       
						                                            
                                                                                                
                                
                        <div class="book_table_item">
                            <button type="submit" class="book_now_btn_black"  >Submit</button>
                        </div>  
						</form>                          
                </div>
            </div>
        </section>                                       
        
        <!--================Introduction Box Area =================-->
        <section class="introduction_box_area">
			<div class="container">
                <div class="explor_title row m0">
                    <div class="pull-left">
                        <div class="left_ex_title">
                            <h2>Top Rated <span>Apartments</span></h2>    
                        </div>
                    </div>
                                   
                </div>                                                               
				<div class="explor_room_item_inner">
                   <div class="mrplsldr">
                        <div class="owl-carousel owl-theme moreplacest" id="morePlaces">
                                                                                                        
                                <?php                            
                                if(!empty($newly_launched)){ 
                                    foreach($newly_launched as $result){
                                        $purl = base_url('property-detail/' . $result['hotelslug']);
                                ?>
                                <div class="item">
                                    <div class="mrpls-grd">
                                        <div class="mrplsimg">
                                            <a  href="javascript:void(0)"><img src="<?php echo $result['featuredimg']; ?>" alt="<?php echo $result['hotelname']; ?>" loading="lazy" /></a>
                                        </div>
                                        <div class="mrplstxt">
                                            <div class="mrplsalt">
                                                <a  target="_blank"  href="javascript:void(0)">
                                                    <h4><?php echo $result['hotelname']; ?></h4>
                                                </a>
                                                <div class="catlgo">
                                                    <span class="whcat"><i class="fa fa-map-marker fa-colour"></i> <?php echo $result['category_name']; ?> - </span>
                                                    <span class="whloc"><?php echo $result['state']; ?></span>
                                                </div>    
                                                <hr class="product-hr">  
                                                <div class="mrpls-prcs">
                                                    <div class="prntg">
                                                        <p><i class="fa fa-inr"></i> <?php echo substr($result['hotelprice'], 0, strpos($result['hotelprice'], ".")); ?>/-</p>  
                                                        <span>For Per Night + Taxes</span>  
                                                    </div>
                                                    <div class="rgtlksmr">   
                                                        <a  class="enquire_now_btn" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">Enquire Now</a>
                                                        <!--<a   href="javascript:void(0)"><i class="fa fa-arrow-right arrowr" aria-hidden="true"></i></a>-->
                                                    </div>
                                                </div>               
                                            </div>
                                        </div>
                                    </div>
                                </div>           
                                <?php } } ?> 
                                   


                            </div>     

                        </div>            
                    </div>                          
                </div>
                                                                                                             
	   </section>
        <!--================End Introduction Box Area =================-->
		                         

     
        <!--================Explor Room Area =================-->
        <section class="explor_room_area">
            <div class="container">
                <div class="explor_title row m0">
                    <div class="pull-left">
                        <div class="left_ex_title">
                           <h2>Top Rated <span>Villas</span></h2>    
                        </div>
                    </div>
                  
                </div>
                <div class="row explor_room_item_inner">
                <div class="mrplsldr">
                        <div class="owl-carousel owl-theme moreplacest" id="morePlaces">
                                                                                                        
                              <?php
                                if(!empty($villas)){ 
                                    foreach($villas as $result){
                                        $purl = base_url('property-detail/' . $result['hotelslug']);
                                ?>
                                <div class="item">
                                    <div class="mrpls-grd">
                                        <div class="mrplsimg">
                                            <a  href="javascript:void(0)"><img src="<?php echo $result['featuredimg']; ?>" alt="<?php echo $result['hotelname']; ?>" loading="lazy" /></a>
                                        </div>
                                        <div class="mrplstxt">
                                            <div class="mrplsalt">
                                                <a   href="javascript:void(0)">
                                                    <h4><?php echo $result['hotelname']; ?></h4>
                                                </a>
                                                <div class="catlgo">
                                                    <span class="whcat"><i class="fa fa-map-marker fa-colour"></i> <?php echo $result['category_name']; ?> - </span>
                                                    <span class="whloc"><?php echo $result['state']; ?></span>
                                                </div>    
                                                <hr class="product-hr">  
                                                <div class="mrpls-prcs">
                                                    <div class="prntg">
                                                        <p><i class="fa fa-inr"></i> <?php echo substr($result['hotelprice'], 0, strpos($result['hotelprice'], ".")); ?>/-</p>  
                                                        <span>For Per Night + Taxes</span>  
                                                    </div>
                                                    <div class="rgtlksmr">  
                                                      <a  class="enquire_now_btn" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">Enquire Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>           
                             <?php } } ?>              
                                            


                            </div>     

                        </div>            
                    </div>
                </div>
            </div>
        </section>
        <!--================End Explor Room Area =================-->
        <section class="our_service_area">
            <div class="container">       
                <div class="row our_service_inner">
                    <div class="col-md-3 col-sm-6">
                        <div class="our_service_first">
                            <h3>Best Amenities</h3>
                            <p>Amenities Curated For Best Vacation Experience</p>  
                        </div>
                    </div>             
                    <div class="col-md-3 col-sm-4">  
                        <div class="our_service_item">
                            <img src="<?php echo base_url('assets_landing_page/')?>img/icon/Outdoor Swimming Pool.png" alt="">
                            <h4>Swimming Pool</h4>             
                        </div>
                    </div>                                                                   
                    <div class="col-md-3 col-sm-4">
                        <div class="our_service_item">    
                            <img src="<?php echo base_url('assets_landing_page/')?>img/icon/Property Manager.png" alt="">
                            <h4>Property Manager</h4>                 
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <div class="our_service_item">
                            <img src="<?php echo base_url('assets_landing_page/')?>img/icon/Concierge Services.png" alt="">
                            <h4>Concierge Services</h4>   
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4">
                        <div class="our_service_item">
                            <img src="<?php echo base_url('assets_landing_page/')?>img/icon/Daily Housekeeping.png" alt="">
                            <h4>Daily Housekeeping</h4>   
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <div class="our_service_item">
                            <img src="<?php echo base_url('assets_landing_page/')?>img/icon/Long Stay Allowed.png" alt="">
                            <h4>Long Stay Allowed</h4>   
                        </div>
                    </div>                 
                    <div class="col-md-3 col-sm-4">
                        <div class="our_service_item">
                            <img src="<?php echo base_url('assets_landing_page/')?>img/icon/Wifi.png" alt="">
                            <h4>Wifi</h4>                              
                        </div>
                    </div>                                 
                </div>
            
			
			</div>
        </section>              
        <section class="introduction_box_area">
			<div class="container">
                <div class="explor_title row m0">
                    <div class="pull-left">
                        <div class="left_ex_title">                            
                            <h2>All <span>Properties</span></h2>       
                        </div>
                    </div>
                   
                </div>                                                               
				<div class="explor_room_item_inner">
                   <div class="mrplsldr">
                        <div class="owl-carousel owl-theme moreplacest" id="morePlaces">
                                                                                                        
                               <?php
                                if(!empty($family_getaways)){ 
                                    foreach($family_getaways as $result){
										                     
                                        $purl = base_url('property-detail/' . $result['hotelslug']);
                                ?>
                                <div class="item">
                                    <div class="mrpls-grd">
                                        <div class="mrplsimg">
                                            <a  href="javascript:void(0)"><img src="<?php echo $result['featuredimg']; ?>" alt="<?php echo $result['hotelname']; ?>" loading="lazy" /></a>
                                        </div>
                                        <div class="mrplstxt">
                                            <div class="mrplsalt">
                                                <a href="javascript:void(0)">
                                                    <h4><?php echo $result['hotelname']; ?></h4>
                                                </a>
                                                <div class="catlgo">
                                                    <span class="whcat"><i class="fa fa-map-marker fa-colour"></i> <?php echo $result['category_name']; ?> - </span>
                                                    <span class="whloc"><?php echo $result['state']; ?></span>
                                                </div>    
                                                <hr class="product-hr">  
                                                <div class="mrpls-prcs">
                                                    <div class="prntg">
                                                        <p><i class="fa fa-inr"></i> <?php echo substr($result['hotelprice'], 0, strpos($result['hotelprice'], ".")); ?>/-</p>  
                                                        <span>For Per Night + Taxes</span>  
                                                    </div>
                                                    <div class="rgtlksmr">
                                                        <a class="enquire_now_btn" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">Enquire Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>           
                             <?php } } ?>        
                                            


                            </div>     

                        </div>            
                    </div>                          
                </div>
                                                                                                             
	   </section>

        
        <!--================Fun Fact Area =================-->
        <section class="fun_fact_area yellow_fun_fact">
            <div class="container">
                <div class="row">
                    <div class="fun_fact_box row m0">
                        <div class="col-md-3 col-sm-12">
                            <div class="media">
                                <div class="media-left">
                                    <h3 class="counter1">150k+</h3>
                                </div>
                                <div class="media-body">
                                    <h4> Guests <br />Hosted</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="media">
                                <div class="media-left">
                                    <h3 class="counter1">30k+</h3>
                                </div>
                                <div class="media-body">
                                    <h4>Bookings <br /></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="media">
                                <div class="media-left">
                                    <h3 class="counter1">4.8</h3>
                                </div>
                                <div class="media-body">
                                    <h4>star avg.<br /> rating</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="media">
                                <div class="media-left">
                                    <h3 class="counter1">30%</h3>
                                </div>
                                <div class="media-body">
                                    <h4>Repeated <br /> Guests</h4>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </section>
		
		
	   
        <!--================End Fun Fact Area =================-->
        
        <!--================Client Testimonial Area =================-->
		<!--================Client Testimonial Area =================-->          
        <section class="client_area client_three">
            <div class="container">                                                      
                <div class="clients_slider owl-carousel">
                    <div class="item">
                        <div class="media">
                            <div class="media-left">
                                <img src="<?php echo base_url('assets_landing_page/')?>img/clients/client-1.png" alt="">
                            </div>
                            <div class="media-body">
                                <p><i>“</i>This exceptional accommodation offers unparalleled comfort and convenience. Its prime location provides easy access to key attractions and urban amenities. Surrounding greenery creates a tranquil and refreshing atmosphere. Guests can enjoy a serene escape while remaining close to the heart of the city. The property truly embodies the perfect blend of nature and urban living.</p>
                                <a href="#"><h4>- Prateek </h4></a>
                                <!--<h5>Tisya Stays</h5>-->
                            </div>
                        </div>         
                    </div>
                    <div class="item">                                
                        <div class="media">
                            <div class="media-left">
                                <img src="<?php echo base_url('assets_landing_page/')?>img/clients/client-2.png" alt="">
                            </div>
                            <div class="media-body">
                                <p><i>“</i>Worth every penny. The team at the property was very helpful and responsible. Immediately responded to all our queries as well. We not only loved the property, but the hospitality as well. Supermarkets and restaurants are on walking distance as well. Definitely recommend!</p>
                                <a href="#"><h4>- Aarushi </h4></a>
                                <!--<h5>Tisya Stays</h5>-->
                            </div>
                        </div>         
                    </div>                 
                    <!--<div class="item">
                        <div class="media">
                            <div class="media-left">
                                <img src="<?php echo base_url('assets_landing_page/')?>img/clients/client-1.png" alt="">
                            </div>
                            <div class="media-body">
                                <p><i>“</i> Lorem ipsum dolor sit amet, cons ectetur elit. Vestibulum nec odios Suspe ndisse cursus mal suada faci lisis. Lorem ipsum dolor sit ametion consectetur elit. Vesti bulum nec odio ipsum. Lorem ipsum dolor sit amet, cons ectetur elit. Vestibulum nec odios Suspe ndisse cursus suada faci lisis. </p>
                                <a href="#"><h4>- GG</h4></a>
                                <h5>Tisya Stays</h5>
                            </div>
                        </div>
                    </div>-->
                </div>
            </div>
        </section>
        <!--================End Client Testimonial Area =================-->
        
        <!--================Latest News Area =================-->
		<?php /* ?>
        <section class="latest_news_area">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row latest_news_left">
                            <div class="left_ex_title">
                                <h2>Latest <span>News & Blog</span></h2>
                            </div>
                            
                            <?php 
                             if(!empty($blog_data)){
                                foreach($blog_data as $result){
                            ?>
                            <div class="col-sm-4">
                                <div class="l_news_item">
                                    <a href="<?= base_url('/blog-details/' . $result['blog_slug']) ?>" class="news_img">
                                        <img src="<?php echo $result['blog_image']; ?>" alt="">
                                    </a>
                                    <div class="news_text">
                                        <a class="l_date" href="#"><?php echo date('d', strtotime($result['added_date'])) ?> <?php echo date('M', strtotime($result['added_date'])) ?></a>
                                        <a href="#"><h4><?php echo $result['blog_title'] ?></h4></a>
                                        <p><?php echo substr(strip_tags($result['blog_content']),0,150); ?></p>
                                        <a class="news_more" href="<?= base_url('/blog-details/' . $result['blog_slug']) ?>">Read more</a>
                                    </div>
                                </div>
                            </div>
                          <?php } } ?>
                          
                           
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>
		<?php */ ?>
        <!--================End Latest News Area =================-->
        
        <!--================Get Contact Area =================-->
        <?php 
        /*
        ?>
        <section class="get_contact_area">
            <div class="container">
                <div class="row get_contact_inner">
                    <div class="col-md-6">
                        <div class="map_box_inner">
                            <div class="iframe-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3844.43598791415!2d73.7719442!3d15.5147411!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bbfc1bd781bf101%3A0x7de45bf748f63db!2sSol%20Banyan%20Grande!5e0!3m2!1sen!2sin!4v1737010120633!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                          
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="right_contact_info">
                            <div class="contact_info_title">
                                <h3>Contact info</h3>
                                <p>Have any Queries? Let us know. We will clear it for you at the best.</p>
                            </div>
                            <div class="contact_info_list">
                                <div class="media">
                                    <div class="media-left">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <div class="media-body">
                                        <h4>Office</h4>
                                        <p>TISYA STAYS PRIVATE LIMITED <br /> House No Plot No B Chalta No 9 P.T.S.149, Next to Hotel Blue Bay, Miramar, Panaji, North Goa, Goa, India, 403001</p>
                                    </div>
                                </div>
                                <div class="media">
                                    <div class="media-left">
                                        <i class="fa fa-envelope-o"></i>
                                    </div>
                                    <div class="media-body">
                                        <h4>Email</h4>
                                        <a href="#">reservations@tisyastays.com</a>
                                    </div>
                                </div>
                                <div class="media">
                                    <div class="media-left">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <div class="media-body">
                                        <h4>Phone</h4>
                                        <a href="#">+918799915100</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php */ ?>     
        <!--================End Get Contact Area =================-->
        
     
        
        <!--================Footer Area =================-->
        <footer class="footer_area">
            <div class="footer_widget_area">
                <div class="container">
                    <div class="row">
                        <div class="col-md-3 col-xs-6">
                            <aside class="f_widget about_widget">
                                <img src="<?php echo base_url('assets_landing_page/')?>img/tisyastays-logo-white.png" alt="">
                                <div class="ab_wd_list">
                                    <div class="media">
                                        <div class="media-left">
                                            <i class="fa fa-map-marker"></i>
                                        </div>
                                        <div class="media-body">
                                            <h4 class="footer-color">House No Plot No B Chalta No 9 P.T.S.149, Next to Hotel Blue Bay, Miramar, Panaji, North Goa, Goa, India, 403001</h4>
                                        </div>
                                    </div>
                                    <div class="media">
                                        <div class="media-left">
                                            <i class="fa fa-phone"></i>
                                        </div>
                                        <div class="media-body">
                                            <h4 class="footer-color">+91 8799915100</h4>
                                        </div>
                                    </div>
                                </div>
                                
                            </aside>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <aside class="f_widget link_widget">
                                <div class="f_title">
                                    <h3>Extra Links</h3>
                                </div>
                                <ul>
                                    <li><a href="#" class="footer-color">-  About Us</a></li>
                                    <li><a href="#" class="footer-color">-  Faq’s</a></li>
                                    <li><a href="#" class="footer-color">-  Blog</a></li>
                                    <li><a href="#" class="footer-color">-  Testimonials</a></li>
                                    <li><a href="#" class="footer-color">-  Reservation Now</a></li>
                                </ul>
                            </aside>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <aside class="f_widget link_widget">
                                <div class="f_title">
                                    <h3>our services</h3>
                                </div>
                                <ul>
                                    <li><a href="#" class="footer-color">-  Food & Drinks</a></li>
                                    <li><a href="#" class="footer-color">-  Rooms</a></li>
                                    <li><a href="#" class="footer-color">-  Amenities</a></li>
                                    <li><a href="#" class="footer-color">-  Spa & Gym</a></li>
                                    <li><a href="#" class="footer-color">-  Hill Tours</a></li>
                                </ul>
                            </aside>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <aside class="f_widget instagram_widget">
                                <div class="f_title">
                                    <h3>Instagram</h3>
                                </div>
                                <ul class="instagram_list" id="instafeed"></ul>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer_copyright_area">
                <div class="container">
                    <div class="pull-left">
                        <h4>Copyright © Tisya Stays  <script>document.write(new Date().getFullYear());</script>. All rights reserved. </h4>
                    </div>
                    <div class="pull-right">
                        <h4>Created by: <a href="#"></a></h4>
                    </div>
                </div>
            </div>
        </footer>
        <!--================End Footer Area =================-->
        
        <!--================Search Box Area =================-->
        <div class="search_area zoom-anim-dialog mfp-hide" id="test-search">
            <div class="search_box_inner">
                <h3>Search</h3>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><i class="icon icon-Search"></i></button>
                    </span>
                </div>
            </div>
        </div>
        <!--================End Search Box Area =================-->
        
        <a href="https://wa.me/918452992240?text=Send a quote" target="_blank" class="float">
           <i class="fa fa-whatsapp my-float"></i>
        </a>
                    
                   
        
        <a href="tel:8799915100" class="float-call">
        <i class="fa fa-phone my-float"></i>
        </a>
      

        <div class="modal fade in" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <p class="text-center">
                        <img src="https://www.tisyastays.com/assest_front/images/tisyastays-logo-green.png" class="img-responsive" alt="Raheja Antares Kanjurmarg" width="200" height="600">
                    </p>       
                    <h5 class="modal-title" id="exampleModalLabel"> <span></span></h5>                
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                </div>
                <div class="modal-body">
                    <form action="" name="form" id="form" method="post">
                    <div class="col-lg-12">
                        <input type="text" name="enqname" id="enqname" class="form-control" placeholder="Enter Full Name" required="">
                    </div>
                    <div class="col-lg-12">
                        <input type="text" name="enqcontactnum" id="enqcontactnum" class="form-control mobile-valid" placeholder="Enter Phone" required="">
                    </div>
                    <div class="col-lg-12">
                        <input type="email" name="enqemail" id="enqemail" class="form-control" placeholder="Enter Email" required="">
                    </div>
                                       
                    <div class="col-lg-12">
                        <input type="submit" class="btn" name="contactbut" id="contactbut" value="Enquire Now">        
                        <input type="checkbox" checked="">
                        <span class="term-condition">By submitting I accept <a href="privacy-policy.php">Privacy Policy.</a></span> </div>
                    </form>
                    <div class="clearfix"></div>
                </div>       
                <div class="modal-footer">
                    <h6><span><i class="fa fa-phone" aria-hidden="true"></i> <a href="tel:8799915100">+91-8799915100</a></span></h6>
                </div>
                </div>
            </div>
            </div>

        
        <!--================End Footer Area =================-->
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="<?php echo base_url('assets_landing_page/')?>js/jquery-2.2.4.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo base_url('assets_landing_page/')?>js/bootstrap.min.js"></script>
        <!-- Rev slider js -->
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/revolution/js/jquery.themepunch.tools.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/revolution/js/jquery.themepunch.revolution.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/revolution/js/extensions/revolution.extension.video.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/revolution/js/extensions/revolution.extension.slideanims.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/revolution/js/extensions/revolution.extension.layeranimation.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/revolution/js/extensions/revolution.extension.navigation.min.js"></script>
        
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/magnify-popup/jquery.magnific-popup.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/isotope/imagesloaded.pkgd.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/isotope/isotope.pkgd.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/counterup/waypoints.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/counterup/jquery.counterup.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/owl-carousel/owl.carousel.min.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/bootstrap-selector/bootstrap-select.js"></script>
        <script src="<?php echo base_url('assets_landing_page/')?>vendors/lightbox/simpleLightbox.min.js"></script>
        
        <!--gmaps Js-->
                                                                        
                   
        <!-- instafeed-->
        <script type="text/javascript" src="<?php echo base_url('assets_landing_page/')?>vendors/instafeed/instafeed.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url('assets_landing_page/')?>vendors/instafeed/script.js"></script>
        
        <script src="<?php echo base_url('assets_landing_page/')?>js/theme.js"></script>
		<script>
		$('.moreplacest').owlCarousel({
			loop:true,                                      
			margin:30,      
			dots: true,                                                                                                                           
			nav:true,        
			autoplay: true,
			navContainerClass: 'explor_room_item_inner',                                                      
            navText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>'],      
			responsiveClass: true,                  				
			responsive:{                         
					0:{
						items:1,
						stagePadding: 40,
						margin:20
					},                                                               
					600:{
						items:3
					},
					1000:{
						items:4
					}
				}			
		});

                                                          

		</script>
        <script>                                           
			$(document).ready(function(){ 
			 setTimeout( function(){ $('#exampleModal').modal('show'); } , 10000 );
				//$('#exampleModal').modal('show');         
			});                                                                              

        </script>
        <script>
            $(document).ready(function() {
            $('.mobile-valid').on('keypress', function(e) {
            var $this = $(this);
            var regex = new RegExp("^[0-9\b]+$");
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            // for 10 digit number only
            if ($this.val().length > 9) {
                e.preventDefault();
                return false;
            }
            if (e.charCode < 54 && e.charCode > 47) {
                if ($this.val().length == 0) {
                    e.preventDefault();
                    return false;
                } else {
                    return true;
                }
            }
            if (regex.test(str)) {
                return true;
            }
            e.preventDefault();
            return false;
            });
            });
            </script>  
    </body>
</html>                        