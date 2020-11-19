var botLinkElements = [
    {
        id : 'ask-about-1',
        url : 'http://www.useragentstring.com/pages/useragentstring.php?name={crawler-name}'
    },
    {
        id : 'ask-about-2',
        url : 'https://www.google.com/search?q={crawler-name} crawler'
    }
];

document.observe("dom:loaded", function() {
    // initialize links
    var askElement, crawlerInput;
    for (var i = 0; i < botLinkElements.length; i++) {
        var el = botLinkElements[i];
        askElement = $(el.id);
        if (askElement == null) {
            continue;
        }
        crawlerInput = askElement.up().previous('input');
        if (crawlerInput == null) {
            continue;
        }
        askElement.href = el.url.replace('{crawler-name}', crawlerInput.value);
        askElement.target = '_blank';
    };
    // observe key press for input
    $('pending_crawler_name').observe('keyup', function(){
        console.log('key pressed');
        var askElement, crawlerInput;
        for (var i = 0; i < botLinkElements.length; i++) {
            var el = botLinkElements[i];
            askElement = $(el.id);
            if (askElement == null) {
                continue;
            }
            crawlerInput = this;
            if (crawlerInput == null) {
                continue;
            }
            askElement.href = el.url.replace('{crawler-name}', crawlerInput.value);
            askElement.target = '_blank';
        }
    });
});
