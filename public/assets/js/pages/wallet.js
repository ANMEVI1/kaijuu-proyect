document.addEventListener('DOMContentLoaded', () => {
    const rateEl = document.getElementById('zen-rate');
    const rate = parseFloat(rateEl?.dataset.rate || '3.5');

    const inputSoles = document.getElementById('input-soles');
    const outputZen = document.getElementById('output-zen');

    if (inputSoles && outputZen) {
        inputSoles.addEventListener('input', (e) => {
            const soles = parseFloat(e.target.value) || 0;
            const zen = Math.floor(soles / rate);
            outputZen.textContent = zen.toLocaleString() + ' ZEN';
        });
    }
});
