import { FormControl, FormErrorMessage, FormLabel } from '@chakra-ui/react';
import { RichTextEditor } from '@/Features/RichTextEditor';
import { FC } from 'react';
import { UseEditPageReturn } from '../../Hooks/useEditPage';

type Props = {
  data: UseEditPageReturn['data'];
  errors: UseEditPageReturn['errors'];
  setData: UseEditPageReturn['setData'];
};

export const HTMLField: FC<Props> = ({ data, errors, setData }) => {
  function handleContentChange(content: string) {
    setData('content', content);
  }
  return (
    <FormControl isInvalid={!!errors.content} isRequired>
      <FormLabel>HTML</FormLabel>
      <RichTextEditor
        defaultValue={data.content}
        setValue={handleContentChange}
      />
      <FormErrorMessage>{errors.content}</FormErrorMessage>
    </FormControl>
  );
};
