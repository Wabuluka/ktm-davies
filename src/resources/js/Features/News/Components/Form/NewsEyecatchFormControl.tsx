import { UseNewsFormReturn } from '@/Features/News/Hooks/useNewsForm';
import { FileInput } from '@/UI/Components/Form/Input/FileInput';
import { ImagePreview } from '@/UI/Components/MediaAndIcons/ImagePreview';
import {
  Button,
  FormControl,
  FormErrorMessage,
  FormLabel,
} from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';

type Props = {
  currentEyecatchUrl?: string;
  error: UseNewsFormReturn['errors']['eyecatch'];
  onUnselect: () => void;
  onChange: (eyecatch: File | null) => void;
} & Omit<ComponentProps<typeof FormControl>, 'children' | 'onChange'>;

export function NewsEyecatchFormControl({
  currentEyecatchUrl,
  error,
  onUnselect,
  onChange,
  ...props
}: Props) {
  const [showCurrentEyecatch, setShowCurrentEyecatch] = useState(
    !!currentEyecatchUrl,
  );
  function handleUnselect() {
    setShowCurrentEyecatch(false);
    onUnselect();
  }
  function handleChange(file: File | null) {
    onChange(file);
  }

  return (
    <FormControl isInvalid={!!error} {...props}>
      <FormLabel>Eyecatch Image</FormLabel>
      {showCurrentEyecatch ? (
        <ImagePreview src={currentEyecatchUrl}>
          <Button onClick={handleUnselect}>Deselect</Button>
        </ImagePreview>
      ) : (
        <FileInput accept="image/*" onChange={handleChange} />
      )}
      <FormErrorMessage>{error}</FormErrorMessage>
    </FormControl>
  );
}
