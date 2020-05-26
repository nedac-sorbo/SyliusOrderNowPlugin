function stopPropagationOnClickOfElementsWithClass(className) {
  const elements = document.getElementsByClassName(className);

  for (let i = 0; i < elements.length; i++) {
    const element = elements.item(i);
    element.onclick = (event) => {
      event.stopPropagation();
    }
  }
}

function submitChildFormOnClickOfProductCardButton() {
  const buttons = document.getElementsByClassName('nedac-order-now-button-container');

  for (let i = 0; i < buttons.length; i++) {
    const button = buttons.item(i);
    const form = button.firstElementChild;

    button.onclick = (event) => {
      form.submit();
    }
  }
}

stopPropagationOnClickOfElementsWithClass('nedac-sylius-order-now-plugin-number-input');
stopPropagationOnClickOfElementsWithClass('nedac-sylius-order-now-plugin-dropdown');
submitChildFormOnClickOfProductCardButton();
