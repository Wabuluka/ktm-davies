import { UseNewsFormReturn } from '@/Features/News/Hooks/useNewsForm';
import { NewsStatus } from '@/Features/News/Types';
import { useDateTimeInput } from '@/Hooks/Form/useDateTimeInput';
import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
} from '@chakra-ui/react';
import { ComponentProps } from 'react';

type Props = {
  status: NewsStatus;
  value: UseNewsFormReturn['data']['published_at'];
  error: UseNewsFormReturn['errors']['published_at'];
  name?: string;
  label?: string;
  onChange: (publishedAt: string) => void;
} & Omit<
  ComponentProps<typeof FormControl>,
  'children' | 'isInvalid' | 'isDisabled' | 'isRequired' | 'onChange'
>;

export function NewsPublishedAtFormControl({
  status,
  value,
  error,
  name = 'published_at',
  label = 'Publish date',
  onChange,
  ...props
}: Props) {
  const publishedAtInput = {
    value,
    ...useDateTimeInput((value) => onChange(value), { min: 'now' }),
  };
  const isInvalid = !!error;
  const isDisabled = status !== 'willBePublished';

  return (
    <FormControl
      isInvalid={isInvalid}
      isDisabled={isDisabled}
      isRequired
      {...props}
    >
      <FormLabel>{label}</FormLabel>
      <Input {...publishedAtInput} name={name} />
      <FormErrorMessage>{error}</FormErrorMessage>
    </FormControl>
  );
}
