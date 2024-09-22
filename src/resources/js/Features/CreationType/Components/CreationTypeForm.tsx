import { useTextInput } from '@/Hooks/Form/useTextInput';
import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';
import { CreationType, CreationTypeFormData } from '../Types';

type Props = {
  creationType?: CreationType;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  errors?: Record<string, string[]>;
  onSubmit: (formData: CreationTypeFormData) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function CreationTypeForm({
  creationType,
  initialFocusRef,
  children,
  errors,
  onSubmit,
  ...props
}: Props) {
  const [formData, setFormData] = useState<CreationTypeFormData>({
    name: creationType?.name || '',
  });
  const nameInput = {
    ref: initialFocusRef,
    value: formData.name,
    ...useTextInput((name) => setFormData({ ...formData, name })),
  };

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit(formData);
  }

  return (
    <form onSubmit={handleSubmit} {...props}>
      <VStack align="stretch">
        <FormControl isRequired isInvalid={!!errors}>
          <FormLabel>Creation Type Name</FormLabel>
          <Input {...nameInput} />
          {errors?.name?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.creationType?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
        {children}
      </VStack>
    </form>
  );
}
