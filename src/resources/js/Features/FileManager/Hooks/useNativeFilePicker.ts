import { useEffect, useRef } from 'react';

type Props = {
  handleChangeInput: EventListener;
  accept?: string;
};

export const useNativeFilePicker = ({ handleChangeInput, accept }: Props) => {
  const inputRef = useRef<HTMLInputElement>();
  const openNativeFilePicker = () => {
    inputRef.current?.setAttribute('accept', accept ?? '*/*');
    inputRef.current?.click();
  };

  useEffect(() => {
    const input = document.createElement('input');
    input.type = 'file';
    input.style.display = 'none';
    input.onchange = handleChangeInput;
    const body = document.body;
    body.insertAdjacentElement('beforeend', input);
    inputRef.current = input;

    return () => {
      input.removeEventListener('change', handleChangeInput);
      input.remove();
      if (inputRef.current) {
        inputRef.current = undefined;
      }
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    const input = inputRef.current;
    if (!input || !input.onchange) return;

    input.removeEventListener('change', input.onchange);
    input.onchange = handleChangeInput;
  }, [handleChangeInput]);

  return openNativeFilePicker;
};
