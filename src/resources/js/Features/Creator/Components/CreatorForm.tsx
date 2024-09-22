import { useTextInput } from '@/Hooks/Form/useTextInput';
import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';
import { Creator, CreatorFormData } from '../Types';

type Props = {
  creator?: Creator;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  errors?: Record<string, string[]>;
  onSubmit: (formData: CreatorFormData) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function CreatorForm({
  creator,
  initialFocusRef,
  errors,
  children,
  onSubmit,
  ...props
}: Props) {
  const [formData, setFormData] = useState<CreatorFormData>({
    name: creator?.name || '',
    name_kana: creator?.name_kana || '',
  });
  const nameInput = {
    value: formData.name,
    ...useTextInput((name) => setFormData({ ...formData, name })),
  };
  const nameKanaInput = {
    value: formData.name_kana,
    ...useTextInput((name_kana) => setFormData({ ...formData, name_kana })),
  };

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit(formData);
  }

  return (
    <form onSubmit={handleSubmit} {...props}>
      <VStack align="stretch">
        <FormControl isRequired isInvalid={!!errors?.name || !!errors?.creator}>
          <FormLabel>Creator Name</FormLabel>
          <Input ref={initialFocusRef} {...nameInput} />
          {errors?.name?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.creator?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
        <FormControl isInvalid={!!errors?.name_kana}>
          <FormLabel>Creator Name (kana)</FormLabel>
          <Input {...nameKanaInput} />
          {errors?.name_kana?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
        {children}
      </VStack>
    </form>
  );
}
