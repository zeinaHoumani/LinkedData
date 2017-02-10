/**
 * 
 */

$(document).ready(function() { 
            
        	$('#form').submit(function(e){
        		
        		e.preventDefault(); // Prevent Default Submission
        		
        		$.ajax({
        			url: 'Country_Table.php',
        			type: 'POST',
        			data: $(this).serialize() // it will serialize the form data
        		})
        		.done(function(data){
        			$('#test').html(data);
        			
        		})
        		.fail(function(){
        			alert('Ajax Submit Failed ...');	
        		});
        	});
        	
        }); 

        $(document).ready(function() { 

        	$.ajax({
    			url: 'Country_Table.php',
    			context: $('#test')})

    			.success(function(data){
                $(this).html(data);})
                .fail(function(){
        			alert('Ajax Submit Failed ...');	
        		});
        });


     
        $(function () {
            $("#next-button").bind("click", function () {
            	
        		$.ajax({
        			url: 'Country_Table.php?next=1',
        			type: 'POST',
        			data: $(this).serialize() // it will serialize the form data
        		})
        		.done(function(data){
                	 $('#test').html(data);
         		
        			
        		})
        		.fail(function(){
        			alert('Ajax Submit Failed ...');	
        		});
            });
        });


        $(function () {
            $("#prev-button").bind("click", function () {
            	
        		$.ajax({
        			url: 'Country_Table.php?prev=1',
        			type: 'POST',
        			data: $(this).serialize() // it will serialize the form data
        		})
        		.done(function(data){
                	 $('#test').html(data);
         			//alert('la liste avant est affiche`');
        			
        		})
        		.fail(function(){
        			alert('Ajax Submit Failed ...');	
        		});
            });
        });


        