import RichTextEditor from '@/Features/RichTextEditor';
import { useTextInput } from '@/Hooks/Form/useTextInput';
import { FormControl, FormLabel, Input, VStack } from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';
import { BlockFormData, BlockOnBookForm } from '../Types';

type Props = {
  customBlock: BlockOnBookForm;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  onSubmit: (formData: BlockFormData) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function CustomBlockForm({
  customBlock: { custom_title = '', custom_content = '' },
  initialFocusRef,
  onSubmit,
  children,
  ...props
}: Props) {
  const [formData, setFormData] = useState<BlockFormData>({
    custom_title,
    custom_content,
  });
  const titleInput = {
    value: formData.custom_title,
    ...useTextInput((custom_title) =>
      setFormData({ ...formData, custom_title }),
    ),
  };

  function setContent(content: string) {
    setFormData({ ...formData, custom_content: content });
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit(formData);
  }

  return (
    <form onSubmit={handleSubmit} {...props}>
      <VStack align="stretch" spacing={8}>
        <FormControl isRequired>
          <FormLabel>Title</FormLabel>
          <Input ref={initialFocusRef} {...titleInput} />
        </FormControl>
        <FormControl>
          <FormLabel>Content</FormLabel>
          <RichTextEditor defaultValue={custom_content} setValue={setContent} />
        </FormControl>
        {children}
      </VStack>
    </form>
  );
}
