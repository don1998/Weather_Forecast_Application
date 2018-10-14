
function myFunction(){
	var city = $("#city").val(); //Value of city entered in input field	
	$.ajax({
		  type: "POST",
		  url: "apicall.php", //endpoint
		  data: {"city":city},
		  success: function(results){
		  	for(var i=0;i<5;i++){
		  		var dayName = "#day"+i;
		  		var dayTemp = "#info"+i;
		  		var dayImg = "#img"+i;
		  		var dayDescr = "#descr"+i;
		  		var date = "#date"+i;
		  		$(dayName).text(results[i].dayofweek); //Populating the HTML elements on the page
		  		$(dayTemp).text(results[i].temperature + ' \u2103');
		  		$(".bold").text("Temp: ");
		  		$(dayImg).attr("src","http://openweathermap.org/img/w/" + results[i].icon + ".png");
		  		$(dayDescr).text(results[i].description.toUpperCase()).css("font-weight","Bold");
		  		$(date).text(results[i].date_only);
		  	}
		  		window.alert("The workers have been notified about schedule changes!"); //Alerting user that the emails have been sent to workers
		  },
		  error: function(requestObject, error, errorThrown) {
            alert(error);
            alert(errorThrown);
       } 
	});

}
