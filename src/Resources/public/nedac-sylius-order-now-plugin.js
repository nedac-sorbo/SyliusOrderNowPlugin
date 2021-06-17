function stopPropagationOnClickOfElementsWithClass(className) {
  const elements = document.getElementsByClassName(className);

  elements.forEach((element) => {
    element.onclick = (event) => {
      event.stopPropagation();
    };
  });
}

function submitChildFormOnClickOfProductCardButton() {
  const buttons = document.getElementsByClassName('nedac-order-now-button-container');

  buttons.forEach((button) => {
    const form = button.firstElementChild;

    button.onclick = () => {
      form.submit();
    };
  });
}

stopPropagationOnClickOfElementsWithClass('nedac-sylius-order-now-plugin-number-input');
stopPropagationOnClickOfElementsWithClass('nedac-sylius-order-now-plugin-dropdown');
submitChildFormOnClickOfProductCardButton();
