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
        excerpt: document.getElementById('post_excerpt')?.value,
        content: document.getElementById('post_content')?.value,
        format: document.getElementById('post_format')?.value,
      },
    );
  };

  // Update every minute (default) or using given interval
  dotclear.wordcount.timer = setInterval(dotclear.wordcount.getCounters, (dotclear.wordcount?.interval || 60) * 1000);
});
