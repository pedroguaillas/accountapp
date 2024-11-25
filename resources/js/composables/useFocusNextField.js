import { nextTick } from "vue";

export const useFocusNextField = () => {
  const focusNextField = (event) => {
    // Obtener todos los elementos que pueden recibir enfoque
    const focusable = Array.from(
      event.target
        .closest("form")
        .querySelectorAll("input, select, textarea")
    ).filter((el) => !el.disabled && !el.hidden);

    // Encontrar el índice del campo actual
    const index = focusable.indexOf(event.target);
    if (index >= 0 && index < focusable.length - 1) {
      nextTick(() => {
        focusable[index + 1].focus();
      });
    }
  };

  return { focusNextField };
};
