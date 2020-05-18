require(["jquery", 'jquery/ui', 'mage/translate', 'mage/url'], function ($, url) {
    $("body").on("change", "#vehicle-fits-make", function () {
        $('.forload .loading-mask').css('display','block');
        $.ajax({
            url: window.BASE_URL + "vehicle_fits/model/modelList",
            data: {"make_id": $(this).val()},
            type: 'get',
            dataType: 'json'
        }).done(function (data) {
            $("#vehicle-fits-model").empty();
            $("#vehicle-fits-year").empty();
            $("#vehicle-fits-model").append($("<option>")
                    .attr('value', "0").text("- " + $.mage.__('Select Model') + " -"));
            $("#vehicle-fits-year").append($("<option>")
                    .attr('value', "0").text("- " + $.mage.__('Select Year') + " -"));
            $(data).each(function () {
                if (this.model_name) {
                    $("#vehicle-fits-model").append($("<option>")
                            .attr('value', this.model_id).text($.mage.__(this.model_name)));
                }
            });

            if ($("#vehicle-fits-make").val()) {
                $("#vehicle-fits-model").removeAttr("disabled");
                $("#vehicle-fits-year").attr("disabled", "");
            } else {
                $("#vehicle-fits-model").attr("disabled", "");
            }
            $('.forload .loading-mask').css('display','none');
            $("#vehicle-fits-model").focus();
        });
       
    });
    $("body").on("change", "#vehicle-fits-model", function () {
        $('.forload .loading-mask').css('display','block');
        $.ajax({
            url: window.BASE_URL + 'vehicle_fits/year/yearList',
            data: {"make_id": $("#vehicle-fits-make").val(), "model_id": $(this).val()},
            type: 'get',
            dataType: 'json'
        }).done(function (data) {
            $("#vehicle-fits-year").empty();
            $("#vehicle-fits-year").append($("<option>")
                    .attr('value', "0").text("- " + $.mage.__('Select Year') + " -"));
            $(data).each(function () {
                if (this.year) {

                    $("#vehicle-fits-year").append($("<option>")
                            .attr('value', this.year_id).text($.mage.__(this.year)));
                }
            });
            if ($("#vehicle-fits-model").val()) {
                $("#vehicle-fits-year").removeAttr("disabled");
            } else {
                $("#vehicle-fits-year").attr("disabled", "");
            }
            $('.forload .loading-mask').css('display','none');
            $("#vehicle-fits-year").focus();
        });
    });

    $("body").on("change", "#vehicle-fits-year", function () {
        if ($("#vehicle-fits-make").val() && $("#vehicle-fits-model").val() && $("#vehicle-fits-year").val()) {
            $('.forload .loading-mask').css('display','block');
            $.ajax({
                type: 'post',
                url: window.BASE_URL + 'vehicle_fits/vehicle/add',
                data: $("#vafForm").serialize(),
                success: function () {
                    location.reload();
                }
            });
        }
    });

    $("body").on("change", "#vehicle-fits-recent", function () {
        if ($("#vehicle-fits-recent").val()) {
            $('.forload .loading-mask').css('display','block');
            $.ajax({
                type: 'post',
                url: window.BASE_URL + 'vehicle_fits/vehicle/add',
                data: $("#vafForm").serialize(),
                success: function () {
                    location.reload();
                }
            });
        }
    });

    $("#btn3").on("click", function (e) {
        e.preventDefault();
        $.ajax({
            url: window.BASE_URL + 'vehicle_fits/vehicle/remove',
            type: 'get',
            dataType: 'json'
        }).done(function (data) {
        });
    });
    $(function () {
        $.ajax({
            url: window.BASE_URL + 'vehicle_fits/vehicle/get',
            type: 'get',
            dataType: 'json'
        }).done(function (data) {
            if(data){
                $(".select_vehicle").append(data);
            }
        });
    });
    $(window).load(function () {
        $("body").on("click",".vehicle_select",function () {
            if($(".selectvehicle").hasClass('active')){
                $(".selectvehicle").removeClass('active');
                $(".selectvehicle").hide();
            }else{
                $(".selectvehicle").addClass('active');
                $(".selectvehicle").show();
            }
            
        });
    });
    $(document).mouseup(function(e){
        var container = $(".select_vehicle");
        if (!container.is(e.target) && container.has(e.target).length === 0){
            $(".selectvehicle").removeClass('active');
            $(".selectvehicle").hide();
        }
    });
});