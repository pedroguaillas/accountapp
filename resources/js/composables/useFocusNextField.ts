import { nextTick } from "vue";

export const useFocusNextField = () => {
  const focusNextField = (event: Event) => {
    // Asumimos que event.target es un HTMLElement
    const target = event.target as HTMLElement | null;
    if (!target) return;

    // Obtener el formulario más cercano
    const form = target.closest("form");
    if (!form) return;

    // Obtener todos los elementos que pueden recibir enfoque
    const focusable = Array.from(
      form.querySelectorAll("input, select, textarea")
    ).filter((el): el is HTMLElement => !el.hasAttribute("disabled") && !el.hasAttribute("hidden"));

    // Encontrar el índice del campo actual
    const index = focusable.indexOf(target);
    if (index >= 0 && index < focusable.length - 1) {
      nextTick(() => {
        focusable[index + 1].focus();
      });
    }
  };

  return { focusNextField };
};
