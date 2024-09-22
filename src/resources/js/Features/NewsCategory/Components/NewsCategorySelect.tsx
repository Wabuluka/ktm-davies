import { NewsCategory } from '@/Features/NewsCategory';
import { Select } from '@chakra-ui/react';
import { ComponentProps } from 'react';

type Props = {
  options: NewsCategory[];
} & Omit<ComponentProps<typeof Select>, 'children'>;

export function NewsCategorySelect({
  options,
  placeholder = 'Please select',
  ...props
}: Props) {
  return (
    <Select placeholder={placeholder} {...props}>
      {options.map((category) => (
        <option key={category.id} value={category.id}>
          {category.name}
        </option>
      ))}
    </Select>
  );
}
