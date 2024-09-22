import { useCallback } from 'react';

export function useSelectInput(onChange: (value: string) => void) {
  const onChangeCallback = useCallback(
    (value: string) => {
      onChange(value);
    },
    [onChange],
  );

  return {
    onChange: onChangeCallback,
  };
}
