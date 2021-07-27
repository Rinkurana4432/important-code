<!DOCTYPE html>
<html lang="en">

<head>
	
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!--title>Sweet Nectar - Something super exiting & delicious is on it's way</title-->
    <link rel="icon" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/logo1.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo esc_url( get_template_directory_uri() ); ?>/images/logo1.ico" type="image/x-icon" />
    <link href="<?php echo esc_url( get_template_directory_uri() ); ?>/css/bootstrap.css" rel="stylesheet" type="text/css" >
	
    <link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() ); ?>/css/style_new.css" type="text/css"/>
	<link href="<?php echo esc_url( get_template_directory_uri() ); ?>/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/bootstrap.min.js"></script>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/icheck.js?v=1.0.2"></script>
	
	<?php wp_head(); ?>
	<script>
	$(document).ready(function(){
	$(".sub-head a").click(function() {
	
		$( ".sub-head a" ).each(function( ) {
          $(this).removeClass('sub-headinner');
        });
         $(this).addClass('sub-headinner');
		});
		});
	$(document).ready(function(){
    $(".flip").click(function(){
        $("#panel").slideToggle("slow");
		//$(".flip").css("border-bottom", "5px dashed #333");
    });
});	
</script>
	
   <script>
$(document).ready(function(){
	//document.getElementById("add_tocrt").disabled = true;
	//$(".checkedshow").css("display","block");
	$(".minus_sign4all").css("display","none");
	$(".circle_div").css("display","none");
	
	
    $(".shopdetailinnerinner").hover(function(){
	$(this).find(".shopdetailinnerhover").css("top","0px");
	 }, function(){
       $(this).find(".shopdetailinnerhover").css("top","100%");
		  
    });
	
	function calculate_sum() {
		var sum = 0;
    	
		$('.prds_qty').each(function() {
        	sum += Number($(this).val());
    	});

		if( sum == 6 ) {
			$(".plus_sign4all").css("display","none");
			//document.getElementById("add_tocrt").disabled = false;
		} else if( sum > 6 ) {
			$(".plus_sign4all").css("display","none");
		} else if( sum < 6 ) {	
			$(".plus_sign4all").css("display","block");
			// document.getElementById("add_tocrt").disabled = true;
		}	
	}	
	
	
	$(".plus-click").click(function(){
		var product_val = $(this).closest(".shopdetailhead").find("input").val();
		var incremented_val = parseInt( product_val ) + 1;
		
			if( incremented_val > 0 ) {
				$(this).closest(".shopdetailhead").find("span").text(incremented_val);
				$(this).closest(".shopdetailhead").find("span").css("display","inline");
			} else {
				
				$(this).closest(".shopdetailhead").find("span").css("display","none");
			}
				
		$(this).closest(".shopdetailhead").find("input").val(incremented_val);
		if( incremented_val > 0 ) { $(this).closest(".shopdetailhead").find(".minus_sign4all").css("display","block"); }

		calculate_sum();
		

	});
	
	$(".minus-click").click(function(){
		var product_val = $(this).closest(".shopdetailhead").find("input").val();
		var Decremented_val = parseInt( product_val ) - 1;
			if( Decremented_val > 0 ) {
				$(this).closest(".shopdetailhead").find("span").text(Decremented_val);
				$(this).closest(".shopdetailhead").find("span").css("display","inline");
			} else {
				$(this).closest(".shopdetailhead").find("span").css("display","none");
			}
		$(this).closest(".shopdetailhead").find("input").val(Decremented_val);
		if( Decremented_val == 0 ) { $(this).closest(".shopdetailhead").find(".minus_sign4all").css("display","none"); }
		
		calculate_sum();
	});	
	
});
	   
	   
	   
	   
</script>
<script>
$(document).ready(function() {
    $('#nut,#cashew,#peanut,#almond').click(function(event) { 
	//on click 
        if(this.checked) { // check select status
            
			 $('#all').each(function() { //loop through each checkbox
                this.checked = false;  //select all checkboxes with class "checkbox1"               
            });
        }
    });
	
	 $('#all').click(function(event) { 
	//on click 
        if(this.checked) { // check select status
            $('.check').each(function() { //loop through each checkbox
                this.checked = false;  //select all checkboxes with class "checkbox1"               
            });
			 
        }
    });

    
});
</script>

 <script>
          $(document).ready(function(){
            var callbacks_list = $('.demo-callbacks ul');
            $('.demo-list input').on('ifCreated ifClicked ifChanged ifChecked ifUnchecked ifDisabled ifEnabled ifDestroyed', function(event){
              callbacks_list.prepend('<li><span>#' + this.id + '</span> is ' + event.type.replace('if', '').toLowerCase() + '</li>');
            }).iCheck({
              checkboxClass: 'icheckbox_square-blue',
              radioClass: 'iradio_square-blue',
              increaseArea: '20%'
            });
          });
          </script>
<script>

function add_to_chkout_product_btn(){
	var sum = 0;
		$('.prds_qty').each(function() {
        	sum += Number($(this).val());
    	});
		
		if(sum < 6){
		$(".myModal").modal('show');
		var add_mr = 6 - sum;	
		$('#qti').html(add_mr);
		return false;
		}
	
	
	
	
	var sum1 = 0;
	var someqty = 0;
	$("#loader").show();
	$("#man1").hide();  
	jQuery.ajax({
		url: "<?php echo get_site_url() ?>/cart?empty-cart",
		type: "post",
		cache: false,
		async: false,
		beforeSend: function(){
   			    $("#loader").show();
				$("#man1").hide();  
   			},
		success: function (htmlStr){						
					//alert(htmlStr);
				}
	});
	$("#loader").show();
	$("#man1").hide();  
	
	$('.prds_qty').each(function() {
		sum1 += Number($(this).val());
		var prdqty = $(this).val();
		if(prdqty > 0) { 
			$("#loader").show();
	$("#man1").hide();
			var prdid = $(this).attr('id');
			call_add_fun(prdid,prdqty);
			var someqty = 785;
		}	
	});
	if( someqty == 785 ) {	window.location.href = "<?php echo get_site_url() ?>/cart"; }
	return false;	
}
	
function call_add_fun(prdid,prdqty){
//alert('new');
	var data_to_send = "product_id=" + prdid +"&quantity=" +prdqty;	
	var id   = $(this).attr('id');
	jQuery.ajax({
			url: "<?php echo get_site_url() ?>/?wc-ajax=add_to_cart",
			type: "post",
			data: data_to_send,
			cache: false,
			async: false,
		      //beforeSend: function(){
   			 // Show image container
    		//	$("#loader").show();
				//$("#man1").hide();  
   			//},
			success: function (htmlStr){
				<?php 
			$user_id = get_current_user_id();
			if ($user_id == 0) { ?>
			window.location.href = "<?php echo get_site_url() ?>/signin";
<?php 
 }else{ ?>
				window.setTimeout(function(){
				window.location.href = "<?php echo get_site_url() ?>/cart";
				 }, 1000);
	<?php } ?>				
					}
	});

	return false;
	
}



	
	
$(document).ready(function(){
$('.flitr_btn').click(function(){
var form = document.myform;
var dataString = $(form).serialize();

jQuery.ajax({
		url: "<?php echo get_site_url() ?>/cart?empty-cart",
		type: "post",
		cache: false,
		async: false,
		success: function (htmlStr){						
					//alert(htmlStr);
				}
	});			
			
$.ajax({
    type:'POST',
     url:'<?php echo get_site_url(); ?>/test',
    data: dataString,
	beforeSend: function(){
   			  //Show image container
		        $(".loader_45").css("height", "260px");   
    			$(".loader_45").show();
		         $('#grid-container').fadeOut('slow');
   			},
    success: function(data){
		$(".loader_45").hide();
		 $('#grid-container').fadeIn('slow');
        $('#grid-container').html(data);
		//alert(data);
		//window.scrollBy(0,250);


    }
});
return false;
		
});

});
	
	
$(document).ready(function(){	
 $("#all").click(function () {
    // $('input:checkbox').not(this).prop('checked', this.checked);
	  });
	$('.check').click(function(){
	$('#all').prop('checked', false)
  });
});	
	
	
</script>
<script>
$(":text").keyup(function (e) {
	  if ($(this).val() != '') {
       $(".cht22").show();
    } 
});


</script>
	<script>
	
			
function goBack() {
   window.history.back();
	//window.history.go(-2)
}
		
$(document).ready(function(){	
 $('.prds_qty').each(function() {
       var prdqty = $(this).val();
	 //alert(prdqty);
	  if(prdqty > 0) { 
		 var prdid = $(this).attr('id');
		 // alert(prdid);
		 var idd = '#' +  $(this).attr('id');
		  //alert(idd + '_pp');
		 $(idd + '_pp').html(prdqty); 
		 $(idd + '_pp').show();
		  $('.plus_sign4all').hide();
		   $(idd + '_mm').show();
		  document.getElementById("add_tocrt").disabled = false;
		  
		}
    });
});

	
</script>	

<script>
function check_postcode(){
	var pincode_2 = $('#pincode').val();
	var data_to_send = "pincode_2="+pincode_2;
	
	 $.ajax({
            url: '<?php echo get_site_url(); ?>/check2',
            type: "post",
            data: data_to_send,
		 	beforeSend: function(){
   			  //Show image container
		        $(".loader_4578").css("height", "260px");   
    			$(".loader_4578").show();
		         $('.chkipin').fadeOut('slow');
				$(".chkipin").height(500);
   			},
            success: function(htmlStr)
            {
              $('.resp').html(htmlStr);
             $('.chkipin').hide();
			$(".loader_4578").hide();	
            }
        });
	
	
	}


	
	
	
	
</script>
<script type="text/javascript">
        $(document).ready(function(){
            $(".slide-toggle").click(function(){
                $("#mainnavbar").slideToggle();
            });
        });
	 $(document).ready(function(){
            $(".slide-toggle2").click(function(){
                $("#mainnavbar_3").slideToggle();
            });
        });
    </script>	
</head>
<body itemscope itemtype="http://schema.org/WebPage" <?php body_class(); ?> dir="<?php if ( is_rtl() ) { echo 'rtl';
} else { echo 'ltr';} ?>">
	 
<?php if(is_front_page()){ ?>
<header class="header">
	  <div class="container">
		 <div class="row">
		    
             <div class="col-md-9 col-sm-8 topmenu">
                <nav class="navbar navbar-default">
					
					  <div class="navbar-header">
						<button type="button" class="slide-toggle2 navbar-toggle collapsed" data-toggle="collapse" data-target="#mainnavbar" aria-expanded="false" aria-controls="navbar">
						  <span class="sr-only">Toggle navigation</span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						</button>
						
					  </div>
					  <div id="mainnavbar_3" class="navbar-collapse collapse">
					   
						<ul class="nav navbar-nav navbar-left">
						   <?php
							wp_nav_menu(
							    array(
							    'theme_location'    => 'primary',
							    'menu_class'        => 'primary-menu small-text',
							    'depth'           	=> 4,
							    'fallback_cb'       => 'azera_shop_wp_page_menu',
							 ));
							?>
						</ul>
					  </div><!--/.nav-collapse -->
					
				</nav>
				 
			 </div>	
			<div class="col-md-3 col-sm-4 rightsec">
			   <div class="socialicon">
			   <a href="https://www.facebook.com/sweetnectardesserts" target="_blank"> <img src="<?php echo get_site_url(); ?>/wp-content/themes/azera-shop/images/facebook.png" class="fblook"></a>
			    <a href="https://www.instagram.com/sweetnectardesserts/" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/themes/azera-shop/images/instagram.png" class="fblook insta"></a>
			   </div>
			   <div class="loginsign dropdown" style="text-align:center">
				  <?php
					if ( is_user_logged_in() ) { ?>
					<a style="font-size:12px;" href="<?php echo get_site_url(); ?>/my-account-2/" class="dropbtn" >MY<br/> ACCOUNT</a>
				   <div class="dropdown-content">
					<a href="<?php echo wp_logout_url( get_home_url() ); ?>">Logout</a>
				</div>
				<?php	} else { ?>
				   <div style="padding:0px 5px!important;">
				   <a href="<?php echo get_site_url(); ?>/signin/" class="dropbtn">LOGIN/<br/>SIGNUP</a>
				   <div class="dropdown-content">
					<!--a href="<?php  //echo home_url(); ?>">HOME</a-->
					<!--a href="<?php //echo get_site_url(); ?>/sign-up/">SIGN UP</a-->
				  </div>
				   </div>	   
				<?php } ?>
			      
				  
			   </div>
			</div>			 
		 </div>
	  </div>
  </header>

<div class="landing-container-section">
    <div class="landing-container-section-inner">
        <?php echo do_shortcode('[rev_slider alias="sliderone"]'); ?>
	  </div>
   </div>
	<div style="clear:both;"></div>
   <div class="full-footer">
     <div class="footerhead">
	    <h2 class="flip">CHECK MY DELIVERY AREA</h2>
		<img src="<?php echo get_site_url(); ?>/wp-content/themes/azera-shop/images/service.png"/>
		<p>AUSTRALIA ONLY</p>
	 </div>
  </div>
<div class="loader_4578" style="display:none;" ></div>	
 <div id="panel" class="chkipin">
	<img src="<?php echo get_site_url(); ?>/wp-content/uploads/2017/08/map.png" class="mp_img">
	 <div>
		 <p class="slide_txt">Delivery area, days & rates</p>
		 <p class="sml_txte">The Shaded area on the map represents roughly where we can deliver to but please enter your area below to double check! </p> 
		 <form name="chk_pin"  method="POST" action="javascript:check_postcode()">
		 <input type="text" Placeholder="Enter your postcode or subrub" name="pincode" id="pincode" class="pin_cls12" style="height:52px!important;border:1px solid #BCEAE7;" required>
			 <button class="btn btn-primary gobtn chk_pin go_bttn" type="submit">GO</button>
		 </form>
	 </div>
</div>
 <div class="resp"></div>	
<?php }  else { ?>
<header class="header">
	  <div class="container">
		 <div class="row">
		     <div class="col-md-2 logo">
                <a href="<?php echo home_url(); ?>"> <img src="<?php echo get_site_url(); ?>/wp-content/themes/azera-shop/images/logo.png"/></a>
			 </div>	
             <div class="col-md-7 col-sm-8 topmenu">
				<nav class="navbar navbar-default">
					
					  <div class="navbar-header">
						
						<button type="button" class="slide-toggle navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar_1" aria-expanded="false" aria-controls="navbar">
						   <span class="sr-only">Toggle navigation</span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						  
						  </button>
						  
					  </div>
					  <div id="mainnavbar" class="navbar-collapse collapse">
					   
						<ul class="nav navbar-nav navbar-left">
						    <li><a href="<?php  echo home_url(); ?>" class="m-top1">Shop my Box</a></li>
							<li><a href="<?php echo get_site_url(); ?>/tell-me-more/"class="m-top1" >Tell me more</a></li>
							<li><a href="<?php echo get_site_url(); ?>/faq/" class="m-top1" >faq</a></li>
							<li><a href="<?php echo get_site_url(); ?>/get-in-touch/" class="m-top1">get in touch</a></li>
					
						</ul>
					  </div><!--/.nav-collapse -->
					
				</nav>
                 
			 </div>	
			<div class="col-md-3 col-sm-4 col-xs-4 rightsec">
			  <div class="socialicon">
			   <a href="https://www.facebook.com/sweetnectardesserts" target="_blank"> <img src="<?php echo get_site_url(); ?>/wp-content/themes/azera-shop/images/facebook.png" class="fblook"></a>
			    <a href="https://www.instagram.com/sweetnectardesserts/" target="_blank"><img src="<?php echo get_site_url(); ?>/wp-content/themes/azera-shop/images/instagram.png" class="fblook insta"></a>
			   </div>
			   <div class="loginsign dropdown" style="text-align:center">
				  <?php
					if ( is_user_logged_in() ) { ?>
					<a style="font-size:12px;" href="<?php echo get_site_url(); ?>/my-account-2/" class="dropbtn" >MY<br/> ACCOUNT</a>
				   <div class="dropdown-content">
					<a href="<?php echo wp_logout_url( get_home_url() ); ?>">Logout</a>
				</div>
				<?php	} else { ?>
				   <div style="padding:0px 5px!important;">
					<a href="<?php echo get_site_url(); ?>/signin/" class="dropbtn">LOGIN/<br/>SIGNUP</a>
				    <div class="dropdown-content">
					<!--a href="<?php //echo home_url(); ?>">HOME</a-->
					<!--a href="<?php //echo get_site_url(); ?>/sign-up/">SIGN UP</a-->
				  </div>
				   </div>
				<?php } ?>
			      
				  
			   </div>
			   </div>
			</div>			 
		 </div>
	  </div>
  </header>



<?php } ?>
