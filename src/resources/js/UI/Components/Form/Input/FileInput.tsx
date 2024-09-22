import { Media } from '@/Features/Media';
import { useImageInputPreview } from '@/UI/Hooks/useImageInputPreview';
import {
  Button,
  HStack,
  Input,
  InputProps,
  VStack,
  useMultiStyleConfig,
} from '@chakra-ui/react';
import { ImagePreview } from '../../MediaAndIcons/ImagePreview';

type Props = {
  initialImagePreview?: Media['original_url'];
  onChange: (image: File | null) => void;
} & Omit<InputProps, 'onChange'>;

export const FileInput = ({
  initialImagePreview,
  onChange,
  ...props
}: Props) => {
  const styles = useMultiStyleConfig('Button', { variant: 'outline' });
  const { imagePreview, setImage, unsetImage, imageRef } =
    useImageInputPreview(initialImagePreview);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const image = e.target.files?.[0];
    if (!image) return;
    if (!image.type.match('image.*')) return;
    onChange(image);
    setImage(image);
  };

  const handleUnselect = () => {
    unsetImage();
    onChange(null);
  };

  return (
    <VStack align="start" spacing={4}>
      {imagePreview && typeof imagePreview === 'string' && (
        <ImagePreview src={imagePreview} alt="">
          <Button onClick={handleUnselect}>Deselect</Button>
        </ImagePreview>
      )}
      <HStack>
        {/* https://github.com/chakra-ui/chakra-ui/issues/457#issuecomment-1290699642 */}
        <Input
          ref={imageRef}
          type="file"
          onChange={handleChange}
          p={0}
          border={0}
          sx={{
            '::file-selector-button': styles,
          }}
          _hover={{ cursor: 'pointer' }}
          {...props}
        />
      </HStack>
    </VStack>
  );
};
