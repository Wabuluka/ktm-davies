import { useCallback } from 'react';

export function useTextInput(onChange: (value: string) => void) {
  const onChangeCallback = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      onChange(e.target.value);
    },
    [onChange],
  );

  return {
    type: 'text',
    onChange: onChangeCallback,
  };
}
