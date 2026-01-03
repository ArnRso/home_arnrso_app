document.addEventListener('DOMContentLoaded', function() {
    const resetBtn = document.getElementById('resetBtn');
    const errorMessage = document.getElementById('errorMessage');

    const valueA = document.getElementById('valueA');
    const valueB = document.getElementById('valueB');
    const valueC = document.getElementById('valueC');
    const resultX = document.getElementById('resultX');

    function hideError() {
        errorMessage.classList.add('d-none');
        errorMessage.textContent = '';
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('d-none');
    }

    function calculateCrossProduct() {
        hideError();

        const a = parseFloat(valueA.value);
        const b = parseFloat(valueB.value);
        const c = parseFloat(valueC.value);

        if (isNaN(a) || isNaN(b) || isNaN(c)) {
            showError('Please enter valid numbers in all fields.');
            resultX.value = '';
            return;
        }

        if (a === 0) {
            showError('Value A cannot be zero (division by zero).');
            resultX.value = '';
            return;
        }

        const x = (b * c) / a;
        resultX.value = x.toString();
    }

    [valueA, valueB, valueC].forEach(input => {
        input.addEventListener('input', function() {
            if (valueA.value && valueB.value && valueC.value) {
                calculateCrossProduct();
            }
        });
    });

    resetBtn.addEventListener('click', function() {
        hideError();
        valueA.value = '';
        valueB.value = '';
        valueC.value = '';
        resultX.value = '';
    });
});
