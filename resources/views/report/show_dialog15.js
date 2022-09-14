function show_dialog15(e){
    sessionStorage.removeItem("modalTrue");
    sessionStorage.setItem("modalTrue",'showTransStartDate');

	let min_date = '{{$first_approved}}' * 1000
    date = new Date();
    start_date_dialog.MAX_DATE = date;
    start_date_dialog.DAYS_DISABLE_MIN = "ON";
    start_date_dialog.DAYS_DISABLE_MAX = "ON";
    start_date_dialog.MIN_DATE = new Date(min_date);


    $('.next-month').off();
    $('.prev-month').off();

    $('.prev-month').click(function () {start_date_dialog.pre_month()});
    $('.next-month').click(function () {start_date_dialog.next_month()});

    start_date_dialog.CURRENT_DATE = new Date();

    if(localStorage.getItem("showCSSStartDate")===null) {
         start_date_dialog.SELECT_DATE = new Date()

    } else{
        var loclaaa=  localStorage.getItem("showCSSStartDate");
        // console.log( loclaaa)
        // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
        start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showCSSStartDate"))
        start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showCSSStartDate"))


        // console.log(start_date_dialog.SELECT_DATE)
    }

	var date =   start_date_dialog.SELECT_DATE.getDate();
	const monthNames = ["January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December"
	];
	var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
	var year = start_date_dialog.SELECT_DATE.getFullYear();
	var select_moth_year  =  month+" "+year
	var date =   start_date_dialog.SELECT_DATE.getDate();
	sessionStorage.setItem("date_check",date);
	sessionStorage.setItem("select_moth_year",select_moth_year);

    if(date == 1){
        start_date_dialog.CURRENT_DATE.setDate(4)
        start_date_dialog.ON_SELECT_FUNC = function(){
            var date = osmanli_calendar.SELECT_DATE;
            localStorage.setItem("showCSSStartDate",date)
            var start_date = dateToYMDEmpty(date);
            // console.log(start_date)

            // localStorage.setItem("sTransDate",start_date)

            $("#stmt_start_date").val(start_date);
			$('input[name="ca_start_date"]').val(start_date);
            jQuery('#showDateModalFrom').modal('hide');
        }

    }else{
        start_date_dialog.ON_SELECT_FUNC = function(){
            var date = osmanli_calendar.SELECT_DATE;
            localStorage.setItem("showCSSStartDate",date)
            var start_date = dateToYMDEmpty(date);
            console.log(start_date)

            // localStorage.setItem("sTransDate",start_date)

            $("#stmt_start_date").val(start_date);
			$('input[name="ca_start_date"]').val(start_date);
            jQuery('#showDateModalFrom').modal('hide');
        }
    }

    start_date_dialog.init()
    if(date == 1){
        var table_data =  $(".date_table tbody tr").eq(1)
        table_data.children('td').each(function(){
        var data = $(this).html();
            if(data== 1){
                $(this).addClass("selected_date")

            }
        })
    }
    jQuery('#showDateModalFrom').modal('show');


	//end showTransStartDate
	var EndDate = new Date();
}

