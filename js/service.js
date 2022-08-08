/*global dotclear */
'use strict';

window.addEventListener('load', () => {
  // Set interval between two counters calculation
  dotclear.wordcount = dotclear.getData('wordcount');

  dotclear.wordcount.getCounters = () => {
    dotclear.services(
      'wordCountGetCounters',
      (data) => {
        const response = JSON.parse(data);
        if (response?.success) {
          if (response?.payload.ret) {
            // Replace current counters
            const p = document.querySelector('div.wordcount details p');
            if (p) {
              p.innerHTML = response.payload.html;
            }
          }
        } else {
          console.log(dotclear.debug && response?.message ? response.message : 'Dotclear REST server error');
          return;
        }
      },
      (error) => {
        console.log(error);
      },
      true, // Use GET method
      {
        json: 1, // Use JSON format for payload
        excerpt: document.querySelector('#post_excerpt').value,
        content: document.querySelector('#post_content').value,
        format: document.querySelector('#post_format').value,
      },
    );
  };

  dotclear.wordcount.timer = setInterval(dotclear.wordcount.getCounters, (dotclear.wordcount?.interval || 60) * 1000);
});
