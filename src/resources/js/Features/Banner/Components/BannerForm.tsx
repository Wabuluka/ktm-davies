import { Banner } from '@/Features/Banner';
import { useBannerForm } from '@/Features/Banner/Hooks/useBannerForm';
import { useEditingBanner } from '@/Features/Banner/Hooks/useEditingBanner';
import { Media } from '@/Features/Media';
import { useCheckBoxInput } from '@/Hooks/Form/useCheckBoxInput';
import { useTextInput } from '@/Hooks/Form/useTextInput';
import { useUrlInput } from '@/Hooks/Form/useUrlInput';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { FileInput } from '@/UI/Components/Form/Input/FileInput';
import { ImagePreview } from '@/UI/Components/MediaAndIcons/ImagePreview';
import {
  Button,
  Checkbox,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';

type Props = {
  data: ReturnType<typeof useBannerForm>['data'];
  errors: ReturnType<typeof useBannerForm>['errors'];
  setData: ReturnType<typeof useBannerForm>['setData'];
  processing: ReturnType<typeof useBannerForm>['processing'];
  previewImageUrl?: Banner['image']['original_url'];
} & ComponentProps<'form'>;

export function BannerForm({
  data,
  errors,
  setData,
  processing,
  ...props
}: Props) {
  const editingBanner = useEditingBanner();
  const [uploadedImage, setUploadedImage] = useState<Media | undefined>(
    editingBanner?.image,
  );
  const nameInput = {
    value: data.name,
    ...useTextInput((name) => setData('name', name)),
  };
  const urlInput = {
    value: data.url,
    ...useUrlInput((url) => setData('url', url)),
  };
  const newTabInput = {
    isChecked: data.new_tab,
    ...useCheckBoxInput((newTab) => setData('new_tab', newTab)),
  };
  const displayedInput = {
    isChecked: data.displayed,
    ...useCheckBoxInput((displayed) => setData('displayed', displayed)),
  };

  function handleImageUnselect() {
    setData('image', { operation: 'stay' });
    setUploadedImage(undefined);
  }

  function handleImageChange(file: File | null) {
    setData('image', file ? { operation: 'set', file } : { operation: 'stay' });
  }

  return (
    <form {...props}>
      <VStack align="stretch" spacing={8}>
        <FormControl isInvalid={!!errors.name} isRequired>
          <FormLabel>Name</FormLabel>
          <Input {...nameInput} />
          <FormErrorMessage>{errors.name}</FormErrorMessage>
        </FormControl>

        <FormControl isInvalid={!!errors.url} isRequired>
          <FormLabel>URL</FormLabel>
          <Input {...urlInput} />
          <FormErrorMessage>{errors.url}</FormErrorMessage>
        </FormControl>

        <FormControl isInvalid={!!errors.new_tab}>
          <FormLabel>Open in new tab</FormLabel>
          <Checkbox {...newTabInput} />
          <FormErrorMessage>{errors.new_tab}</FormErrorMessage>
        </FormControl>

        <FormControl isInvalid={!!errors.displayed}>
          <FormLabel>Displayed</FormLabel>
          <Checkbox {...displayedInput} />
          <FormErrorMessage>{errors.displayed}</FormErrorMessage>
        </FormControl>

        <FormControl isInvalid={!!errors['image.file']} isRequired>
          <FormLabel>Image</FormLabel>
          {uploadedImage?.original_url ? (
            <ImagePreview
              src={uploadedImage.original_url}
              alt="chosen thumbnail"
            >
              <Button onClick={handleImageUnselect}>Deselect</Button>
            </ImagePreview>
          ) : (
            <FileInput
              accept="image/*"
              alt="chosen thumbnail"
              onChange={handleImageChange}
            />
          )}
          <FormErrorMessage>{errors['image.file']}</FormErrorMessage>
        </FormControl>

        <PrimaryButton type="submit" isLoading={processing}>
          Save
        </PrimaryButton>
      </VStack>
    </form>
  );
}
