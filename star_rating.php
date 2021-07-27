<?php

//For Push array

	 var a = {};
			a["price"] = price;
			a["prod_id"] = prod_id;
			a["prod_name"] = prod_name;
		 
			 product_ids.push(a);
			 // product_ids is varialble in which its array stored 
			 
			 
	?>		 
			 
		//star rating	 
			 <div class="stars">
				  <ul>
					<li><span data-rating="1" class="star glyphicon glyphicon-star"></span></li>
					<li><span data-rating="2" class="star glyphicon glyphicon-star"></span></li>
					<li><span data-rating="3" class="star glyphicon glyphicon-star"></span></li>
					<li><span data-rating="4" class="star glyphicon glyphicon-star"></span></li>
					<li><span data-rating="5" class="star glyphicon glyphicon-star"></span></li>
				  </ul>
				</div>
				<div class="rating" style="display:none;">
				  <span data-rating="1" class="message -poor">1</span>
				  <span data-rating="2" class="message -bad">2</span>
				  <span data-rating="3" class="message -average">3</span>
				  <span data-rating="4" class="message -good">4</span>
				  <span data-rating="5" class="message -awesome">5</span>
				</div>
				<script>
var 
    // Stars
    stars = $('.stars'),
    star  = stars.find('.star'),
    // Messages
    rating = $('.rating'),
    // 3 star information block
    information = $('.information'),
    // Comment block
    comment = $('.comment');

star.on('click', function() {
  var that  = $(this),
      value = that.data()['rating'];
      jQuery('#rateit').val(value);
  // Remove class for selected stars
  stars.find('.-selected').removeClass('-selected');

  // Add class to the selected star and all before
  for (i = 1; i <= value; i++) {
    stars.find('[data-rating="' + i + '"]').addClass('-selected');
  }

  // Show text that explains the rating value
  rating.find('.-visible').removeClass('-visible');
  rating.find('[data-rating="' + value + '"]').addClass('-visible');

  // Show information block if value is 3
  if (value === 3) {
    information.show();
  } else {
    information.hide();
  }

  // Show comments block, if value is 3 or lower
  if (value <= 3) {
    comment.show();
  } else {
    comment.hide();
  }
});
</script>
<style>
/*!
 * bootstrap-star-rating v4.0.3
 * http://plugins.krajee.com/star-rating
 *
 * Author: Kartik Visweswaran
 * Copyright: 2013 - 2017, Kartik Visweswaran, Krajee.com
 *
 * Licensed under the BSD 3-Clause
 * https://github.com/kartik-v/bootstrap-star-rating/blob/master/LICENSE.md
 */
.stars {
  display: inline-block;
  position: relative;
  vertical-align: middle;
  font-size: 3em;
}

.stars ul {
  white-space: nowrap;
  list-style: none;
  padding: 0;
}

.stars li {
  float: left;
}

.star {
  color: silver;
  cursor: pointer;
  padding: 0 2px;
}

.star.-selected {
  color: yellow;
}

.comment,
.information {
  display: none;
  padding: 5px 10px;
}

.comment {
  background: aqua;
}

.information {
  background: lightgreen;
}

.rating {
  display: inline-block;
  vertical-align: middle;
}

.message {
  display: none;
}

.message.-visible {
  display: block;
}




</style>
//star rating

<?php 
//This function added in comman file like database connection files
function add_auditlog($data_arr){
	//Set logs - start
	if(is_array($data_arr) && count($data_arr) > 0){
		$to_print = "Date: ".date("F j, Y, g a")."\r\n";
		foreach($data_arr as $key=>$value){
			$to_print .= $key." : ".$value."\r\n";
		}
		$to_print .= '*****************************************'."\r\n";
		//Save string to log, use FILE_APPEND to append.
		file_put_contents('debubLog.txt', $to_print, FILE_APPEND); 
	}
	//Set logs - end
}
// $data_arrt = array('Name'=>'abc','Class'=>'Test');
// add_auditlog($data_arrt);




$data_arrt = array('Log Type'=>'Project Updated','Updated by'=>$current_username.'('.$user_id.')','Project Name'=>$name,'Assigned client'=>$name22['fname'],'Description'=>$description);//add Parammerters and detail jo add karni hai file main
add_auditlog($data_arrt);//Call function







