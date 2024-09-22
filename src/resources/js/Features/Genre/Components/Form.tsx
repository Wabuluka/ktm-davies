import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, FC, useState } from 'react';

export type formValues = {
  name: string;
};

type Props = {
  initialValues?: formValues;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  errors?: Record<string, string[]>;
  onSubmit: (e: React.FormEvent<HTMLFormElement>, genre: formValues) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

const defaultInitialValues: formValues = { name: '' };

export const Form: FC<Props> = ({
  initialValues = defaultInitialValues,
  errors,
  initialFocusRef,
  onSubmit,
  ...props
}) => {
  const [genre, setGenre] = useState<formValues>(initialValues);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setGenre((prevState) => ({ ...prevState, [name]: value }));
  };

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    onSubmit(e, genre);
  };

  return (
    <VStack align="stretch">
      <form {...props} onSubmit={handleSubmit}>
        <FormControl isInvalid={!!errors} isRequired>
          <FormLabel>Genre Name</FormLabel>
          <Input
            ref={initialFocusRef}
            type="text"
            name="name"
            value={genre?.name ?? ''}
            onChange={handleChange}
          />
          {errors?.name?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.genre?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
      </form>
    </VStack>
  );
};
