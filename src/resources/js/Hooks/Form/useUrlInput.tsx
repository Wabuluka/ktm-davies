import { useCallback } from 'react';

export function useUrlInput(onChange: (value: string) => void) {
  const onChangeCallback = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      onChange(e.target.value);
    },
    [onChange],
  );

  return {
    type: 'url',
    onChange: onChangeCallback,
  };
}
