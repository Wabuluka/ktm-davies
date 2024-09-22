import {
  Button,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, FC, useState } from 'react';
import { FileInput } from '@/UI/Components/Form/Input/FileInput';
import { ImagePreview } from '@/UI/Components/MediaAndIcons/ImagePreview';
import { Character, CharacterFormData } from '@/Features/Character';
import { useTextInput } from '@/Hooks/Form/useTextInput';
import { SelectSeriesDrawer } from '@/Features/Series';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { SeriesSelection } from '@/Features/Series/Components/SeriesSelection';

type Props = {
  character?: Character;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  errors?: Record<string, string[]>;
  onSubmit: (formData: CharacterFormData) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export const Form: FC<Props> = ({
  character,
  errors,
  initialFocusRef,
  onSubmit,
  ...props
}) => {
  const [uploadedThumbnail, setUploadedThumbnail] = useState(
    character?.thumbnail,
  );

  const [formData, setFormData] = useState<CharacterFormData>({
    name: character?.name ?? '',
    description: character?.description ?? '',
    series_id: character?.series?.id ?? undefined,
    thumbnail: {
      operation: 'stay',
    },
  });

  const nameInput = {
    value: formData.name,
    ...useTextInput((name) => setFormData({ ...formData, name })),
  };

  const descriptionInput = {
    value: formData.description,
    ...useTextInput((description) => setFormData({ ...formData, description })),
  };

  function handleSeriesChange(series_id?: number) {
    setFormData({ ...formData, series_id });
  }

  function handleThumbnailChange(file: File | null) {
    setFormData({
      ...formData,
      thumbnail: file ? { operation: 'set', file } : { operation: 'stay' },
    });
  }

  function handleThumbnailUnselect() {
    setFormData({ ...formData, thumbnail: { operation: 'delete' } });
    setUploadedThumbnail(undefined);
  }

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit(formData);
  }

  return (
    <VStack align="stretch">
      <form {...props} onSubmit={handleSubmit}>
        <FormControl
          isInvalid={!!errors?.name || !!errors?.character}
          isRequired
        >
          <FormLabel>Character Name</FormLabel>
          <Input ref={initialFocusRef} {...nameInput} />
          {errors?.name?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.character?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl isInvalid={!!errors?.description}>
          <FormLabel>Character Description</FormLabel>
          <Input {...descriptionInput} />
          {errors?.description?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl isInvalid={!!errors?.series_id}>
          <FormLabel>Related Series</FormLabel>
          {formData.series_id && (
            <SeriesSelection
              seriesId={formData.series_id}
              onUnselect={() => handleSeriesChange()}
            />
          )}
          <SelectSeriesDrawer
            onSubmit={handleSeriesChange}
            selectedSeriesId={formData.series_id}
            renderOpenDrawerElement={(onOpen) => (
              <PrimaryButton onClick={onOpen}>Select</PrimaryButton>
            )}
          />
          {errors?.series_id?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl
          isInvalid={
            !!errors?.['thumbnail.operation' || !!errors?.['thumbnail.file']]
          }
        >
          <FormLabel>Thumbnail</FormLabel>
          {uploadedThumbnail?.original_url ? (
            <ImagePreview
              src={uploadedThumbnail?.original_url}
              alt="選択済みのサムネイル"
            >
              <Button onClick={handleThumbnailUnselect}>Deselect</Button>
            </ImagePreview>
          ) : (
            <FileInput
              accept="image/*"
              alt="選択済みのサムネイル"
              onChange={handleThumbnailChange}
            />
          )}
          {errors?.['thumbnail.operation']?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.['thumbnail.file']?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
      </form>
    </VStack>
  );
};
