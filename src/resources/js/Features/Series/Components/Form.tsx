import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, FC, useState } from 'react';

type formValues = {
  name: string;
};

type Props = {
  initialValues?: formValues;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  errors?: Record<string, string[]>;
  onSubmit: (
    e: React.FormEvent<HTMLFormElement>,
    series: { name: string },
  ) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

const defaultInitialValues: formValues = { name: '' };
export const Form: FC<Props> = ({
  initialValues = defaultInitialValues,
  errors,
  initialFocusRef,
  onSubmit,
  ...props
}) => {
  const [series, setSeries] = useState<formValues>(initialValues);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setSeries({ [name as 'name']: value });
  };

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    onSubmit(e, series);
  };

  return (
    <VStack align="stretch">
      <form {...props} onSubmit={handleSubmit}>
        <FormControl isInvalid={!!errors} isRequired>
          <FormLabel>Series Name</FormLabel>
          <Input
            ref={initialFocusRef}
            type="text"
            name="name"
            value={series?.name ?? ''}
            onChange={handleChange}
          />
          {errors?.name?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.series?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
      </form>
    </VStack>
  );
};
