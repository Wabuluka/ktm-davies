import { UseEditPageReturn } from '@/Features/Page/Hooks/useEditPage';
import {
  FormControl,
  FormControlProps,
  FormErrorMessage,
  FormLabel,
  Input,
} from '@chakra-ui/react';
import { FC, useCallback } from 'react';

type Props = {
  data: UseEditPageReturn['data'];
  errors: UseEditPageReturn['errors'];
  setData: UseEditPageReturn['setData'];
} & FormControlProps;

export const SlugField: FC<Props> = ({ data, errors, setData, ...props }) => {
  const onChangeSlug = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => setData('slug', e.target.value),
    [setData],
  );

  return (
    <FormControl isInvalid={!!errors.slug} isRequired {...props}>
      <FormLabel>Slug</FormLabel>
      <Input
        type="text"
        value={data.slug}
        onChange={onChangeSlug}
        sx={{
          _readOnly: { bg: 'gray.50' },
        }}
      />
      <FormErrorMessage>{errors.slug}</FormErrorMessage>
    </FormControl>
  );
};
