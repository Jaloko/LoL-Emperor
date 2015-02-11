function convertDate() {
	for(var i = 0; i < 10; i++) {
	    var divUtc = $('#date' + i);
	    var divLocal = $('#date' + i); 

	    var timestamp = divUtc.text().trim(); 
	    divUtc.text(moment.utc(parseInt(timestamp)).format('YYYY-MM-DD HH:mm:ss'));
	    var localTime = moment.utc(divUtc.text()).toDate();
	    localTime = moment(localTime).format('DD/MM/YY h:mm:ss a');
	    divLocal.text(localTime);     
	}
}