import { useTextInput } from '@/Hooks/Form/useTextInput';
import { FileInput } from '@/UI/Components/Form/Input/FileInput';
import { ImagePreview } from '@/UI/Components/MediaAndIcons/ImagePreview';
import {
  Button,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, PropsWithChildren, useState } from 'react';
import { ExternalLink, ExternalLinkFormData } from '../Types';

type Props = {
  onSubmit: (formData: ExternalLinkFormData) => void;
  errors?: Record<string, string[]>;
  externalLink?: ExternalLink;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function ExternalLinkForm({
  externalLink,
  errors,
  onSubmit,
  children,
  ...props
}: PropsWithChildren<Props>) {
  const [uploadedThumbnail, setUploadedThumbnail] = useState(
    externalLink?.thumbnail,
  );
  const [formData, setFormData] = useState<ExternalLinkFormData>(
    externalLink
      ? {
          title: externalLink.title,
          url: externalLink.url,
          thumbnail: {
            operation: 'stay',
          },
        }
      : {
          title: '',
          url: '',
          thumbnail: {
            operation: 'stay',
          },
        },
  );
  const titleInput = {
    value: formData.title,
    ...useTextInput((title) => setFormData({ ...formData, title })),
  };
  const urlInput = {
    value: formData.url,
    ...useTextInput((url) => setFormData({ ...formData, url })),
  };
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
    <form onSubmit={handleSubmit} {...props}>
      <VStack>
        <FormControl
          isInvalid={!!errors?.title || !!errors?.externalLink}
          isRequired
        >
          <FormLabel>Title</FormLabel>
          <Input {...titleInput} />
          {errors?.title?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.externalLink?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
        <FormControl isInvalid={!!errors?.url} isRequired>
          <FormLabel>URL</FormLabel>
          <Input {...urlInput} />
          {errors?.url?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>
        <FormControl
          isInvalid={
            !!errors?.['thumbnail.operation'] || !!errors?.['thumbnail.file']
          }
        >
          <FormLabel>Thumbnail</FormLabel>
          {uploadedThumbnail ? (
            <ImagePreview
              src={uploadedThumbnail?.original_url}
              alt="chosen thumbnail"
            >
              <Button onClick={handleThumbnailUnselect}>Deselect</Button>
            </ImagePreview>
          ) : (
            <FileInput
              accept="image/*"
              alt="chosen thumbnail"
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
        {children}
      </VStack>
    </form>
  );
}
