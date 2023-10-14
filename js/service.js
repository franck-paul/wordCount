/*global dotclear */
'use strict';

window.addEventListener('load', () => {
  // Set interval between two counters calculation
  dotclear.wordcount = dotclear.getData('wordcount');

  dotclear.wordcount.getCounters = () => {
    dotclear.jsonServicesPost(
      'wordCountGetCounters',
      (payload) => {
        if (payload.ret) {
          // Replace current counters
          const p = document.querySelector('div.wordcount details p');
          if (p) {
            p.innerHTML = payload.html;
          }
        }
      },
      {
        excerpt: document.querySelector('#post_excerpt').value,
        content: document.querySelector('#post_content').value,
        format: document.querySelector('#post_format').value,
      },
    );
  };

  dotclear.wordcount.timer = setInterval(dotclear.wordcount.getCounters, (dotclear.wordcount?.interval || 60) * 1000);
});
