function countdown(time, htmlId, finishFunction) {
    const countdownTime = new Date(time).getTime();
    let interval = setInterval(function () {
        const now = new Date().getTime();
        const timeLeft = countdownTime - now;

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000).toString().padStart(2, '0');

        let text = '';
        if (days > 0) {
            text = days + " dní ";
        }
        text += hours + ":" + minutes + ":" + seconds;
        document.getElementById(htmlId).innerHTML = text;

        if (timeLeft < 0) {
            clearInterval(interval);
            finishFunction();
            if (typeof finishFunction === 'function') {
                finishFunction();
            }
        }
    }, 1000);
}
