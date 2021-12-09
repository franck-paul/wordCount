/*global $, dotclear */
'use strict';

dotclear.wordCountGetCounters = () => {
  $.get('services.php', {
    f: 'wordCountGetCounters',
    xd_check: dotclear.nonce,
    excerpt: $('#post_excerpt').val(),
    content: $('#post_content').val(),
    format: $('#post_format').val(),
  })
    .done((data) => {
      if ($('rsp[status=failed]', data).length > 0) {
        // For debugging purpose only:
        // window.console.log($('rsp', data).attr('message'));
        window.console.log('Dotclear REST server error');
      } else {
        const ret = Number($('rsp>check', data).attr('ret'));
        if (ret) {
          const html = $('rsp>check', data).attr('html');
          const $container = $('div.wordcount details p');
          if ($container) {
            // Replace current counters
            $container.empty().append(html);
          }
        }
      }
    })
    .fail((jqXHR, textStatus, errorThrown) => {
      window.console.log(`AJAX ${textStatus} (status: ${jqXHR.status} ${errorThrown})`);
    })
    .always(() => {
      // Nothing here
    });
};

$(() => {
  // Set 30 seconds interval between two counters calculation
  dotclear.wordCountGetCounters_Timer = setInterval(dotclear.wordCountGetCounters, 60 * 1000);
});
