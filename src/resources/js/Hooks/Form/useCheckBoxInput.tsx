import { useCallback } from 'react';

export function useCheckBoxInput(onChange: (value: boolean) => void) {
  const onChangeCallback = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      onChange(e.target.checked);
    },
    [onChange],
  );

  return {
    onChange: onChangeCallback,
  };
}
