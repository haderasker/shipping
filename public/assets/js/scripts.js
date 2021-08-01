
  (function(window, undefined) {
    'use strict';

    /*
    NOTE:
    ------
    PLACE HERE YOUR OWN JAVASCRIPT CODE IF NEEDED
    WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR JAVASCRIPT CODE PLEASE CONSIDER WRITING YOUR SCRIPT HERE.  */

    $(document).ajaxStart(function () {
      showLoading();
    });
    $(document).ajaxComplete(function() {
      console.log("hide complete");
      hideLoading();
    });
    $(document).ajaxError(function() {
      console.log("hide error");
      hideLoading();
    });

    $(document).on("wheel", "input[type=number]", function (e) {
      $(this).blur();
    });

  })(window);

function handleAjaxResponse(response,redirect) {

    if (response === 'true') {
      if (redirect)
        location.href = redirect;
      else
        location.reload();
    }
    else{
    // Display an error toast, with a title
    toastr.error(response, 'Error');
    }
  }

  function showLoading(){
    $('#modal-loading').modal('show');
  }
  function hideLoading(){
    setTimeout(function() {
      $('#modal-loading').modal('hide');
      }, 1000);
  }

  function sformat(s) {

    // create array of day, hour, minute and second values
    var fm = [
      Math.floor(s / (3600 * 24)),
      Math.floor(s % (3600 * 24) / 3600),
      Math.floor(s % 3600 / 60),
      Math.floor(s % 60)
    ];

    // map over array
    return $.map(fm, function(v, i) {

      // if a truthy value
      if (Boolean(v)) {

        // add the relevant value suffix
        if (i === 0) {
          v = plural(v, "day");
        } else if (i === 1) {
          v = plural(v, "hour");
        } else if (i === 2) {
          v = plural(v, "minute");
        } else if (i === 3) {
          v = plural(v, "second");
        }

        return v;
      }

    }).join(', ');

    function plural(value, unit) {

      if (value === 1) {
        return value + " " + unit;
      } else if (value > 1) {
        return value + " " + unit + "s";
      }

    }
  }

  function getCurrentTime(tz) {
    // suppose the date is 12:00 UTC
    var d = new Date();
    var invdate = new Date(d.toLocaleString('en-US', {
      timeZone: tz
    }));

    // then invdate will be 07:00 in Toronto
    // and the diff is 5 hours
    var diff = d.getTime() - invdate.getTime();

    // so 12:00 in Toronto is 17:00 UTC
    var result = new Date(d.getTime() - diff); // needs to substract
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    return days[result.getDay()] +' '+ result.getDate() + ' - ' + formatAMPM(result);
    // return days[result.getDay()] +' '+ result.getDate() + ' - ' + result.getHours()+':'+result.getMinutes() + ':' + result.getSeconds();
  }
  function validURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
  }
  function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
  }
