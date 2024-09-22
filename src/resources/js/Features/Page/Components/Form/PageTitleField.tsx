import { UseEditPageReturn } from '@/Features/Page/Hooks/useEditPage';
import {
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
} from '@chakra-ui/react';
import { FC, useCallback } from 'react';

type Props = {
  data: UseEditPageReturn['data'];
  errors: UseEditPageReturn['errors'];
  setData: UseEditPageReturn['setData'];
};

export const PageTitleField: FC<Props> = ({ data, errors, setData }) => {
  const onChangeTitle = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) =>
      setData('title', e.target.value),
    [setData],
  );

  return (
    <FormControl isInvalid={!!errors.title} isRequired>
      <FormLabel>Title</FormLabel>
      <Input
        type="text"
        size="lg"
        fontSize={{ base: 28, lg: 36 }}
        value={data.title}
        onChange={onChangeTitle}
      />
      <FormErrorMessage>{errors.title}</FormErrorMessage>
    </FormControl>
  );
};
