
var loadAllAnalyticsBadgeInfo = function () {
  $.ajax({
    url: base + 'main-layout/badge-info',
    method: 'GET',
    success: function(res) {
      $("#pre-analytics-badge").html(res.pre_analytics);
    },
    error: function (request, status, error) {
    
    }

  })
}

document.addEventListener('DOMContentLoaded', function () {
  loadAllAnalyticsBadgeInfo();
});