<!--Script For Searching-->
<script>
$(document).ready(function()
{
	$('#search').keyup(function()//here add input type id
	{
	searchTable($(this).val());
	var ddt  = $("#search").val();
	if(ddt == ''){
		location.reload();
	}
	});
	
	
});

function searchTable(inputVal)
{
	var table = $('.tblData');//And here add table class those table you want to search data 
	table.find('tr').each(function(index, row)
	{
		var allCells = $(row).find('td');
		if(allCells == ''){
			
		}
		if(allCells.length > 0)
		{
			var found = false;
			allCells.each(function(index, td)
			{
				var regExp = new RegExp(inputVal, 'i');
				if(regExp.test($(td).text()))
				{
					found = true;
					return false;
					
				}
			});
			if(found == true)
				$(row).show();
			else
				$(row).hide();
			    
		}
	});
}
</script>
<!--Script For Searching End-->