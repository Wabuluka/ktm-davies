import {
  Button,
  ButtonProps,
  Image,
  ImageProps,
  Popover,
  PopoverArrow,
  PopoverBody,
  PopoverCloseButton,
  PopoverContent,
  PopoverProps,
  PopoverTrigger,
} from '@chakra-ui/react';
import { ImagePreview } from './ImagePreview';

type Props = Omit<PopoverProps, 'children'> & {
  previewTriggerProps: Omit<ButtonProps, 'children'>;
  imageProps: Omit<ImageProps, 'w' | 'width' | 'h' | 'height'>;
};

export function PreviewableThumbnail({
  previewTriggerProps,
  imageProps,
}: Props) {
  return (
    <Popover placement="right-start">
      <PopoverTrigger>
        <Button {...previewTriggerProps}>
          <Image
            maxW="100%"
            maxH="100%"
            borderRadius="full"
            alt=""
            {...imageProps}
          />
        </Button>
      </PopoverTrigger>
      <PopoverContent>
        <PopoverArrow bg="gray.700" />
        <PopoverCloseButton bg="white" size="lg" _hover={{ bg: 'gray.300' }} />
        <PopoverBody p={1} bg="gray.700">
          <ImagePreview alt="" {...imageProps} />
        </PopoverBody>
      </PopoverContent>
    </Popover>
  );
}
