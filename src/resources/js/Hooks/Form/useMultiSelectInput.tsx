import { useCallback } from 'react';

type Value = string[];

export function useMultiSelectInput(onChange: (value: Value) => void) {
  const onChangeCallback = useCallback(
    (value: Value) => {
      onChange(value);
    },
    [onChange],
  );

  return {
    onChange: onChangeCallback,
  };
}
