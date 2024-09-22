import { formatInTimeZone } from 'date-fns-tz';
import { useCallback } from 'react';

export function useDateTimeInput(
  onChange: (value: string) => void,
  options: {
    min?: 'now' | number;
    max?: 'now' | number;
  } = {},
) {
  const onChangeCallback = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      onChange(e.target.value);
    },
    [onChange],
  );

  const now = formatInTimeZone(new Date(), 'Asia/Tokyo', "yyyy-MM-dd'T'HH:mm");

  return {
    type: 'datetime-local',
    onChange: onChangeCallback,
    min: options.min === 'now' ? now : options.min,
    max: options.max === 'now' ? now : options.max,
  };
}
