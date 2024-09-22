import { Benefit, BenefitFormData } from '@/Features/Benefit';
import { GoodsStoreSelect } from '@/Features/GoodsStore';
import { useCheckBoxInput } from '@/Hooks/Form/useCheckBoxInput';
import { useTextInput } from '@/Hooks/Form/useTextInput';
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
import { ComponentProps, FC, useState } from 'react';

type Props = {
  benefit?: Benefit;
  initialFocusRef?: React.LegacyRef<HTMLInputElement>;
  errors?: Record<string, string[]>;
  onSubmit: (formData: BenefitFormData) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export const Form: FC<Props> = ({
  benefit,
  errors,
  initialFocusRef,
  onSubmit,
  ...props
}) => {
  const [uploadedThumbnail, setUploadedThumbnail] = useState(
    benefit?.thumbnail,
  );

  const [formData, setFormData] = useState<BenefitFormData>({
    name: benefit?.name ?? '',
    paid: benefit?.paid ?? false,
    storeId: benefit?.store.id ?? undefined,
    thumbnail: {
      operation: 'stay',
    },
  });

  const nameInput = {
    value: formData.name,
    ...useTextInput((name) => setFormData({ ...formData, name })),
  };

  const paidInput = {
    isChecked: formData.paid,
    ...useCheckBoxInput((paid) => setFormData({ ...formData, paid })),
  };

  function handleStoreChange(e: React.ChangeEvent<HTMLSelectElement>) {
    setFormData({ ...formData, storeId: Number(e.target.value) });
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
        <FormControl isInvalid={!!errors?.name || !!errors?.benefit} isRequired>
          <FormLabel>Benefit Name</FormLabel>
          <Input ref={initialFocusRef} {...nameInput} />
          {errors?.name?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
          {errors?.benefit?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl isInvalid={!!errors?.paid}>
          <FormLabel>Paid</FormLabel>
          <Checkbox {...paidInput} />
          {errors?.paid?.map((message, index) => (
            <FormErrorMessage key={message + index}>{message}</FormErrorMessage>
          ))}
        </FormControl>

        <FormControl isInvalid={!!errors?.store_id} isRequired>
          <FormLabel>Goods Store</FormLabel>
          <GoodsStoreSelect
            value={formData.storeId}
            onChange={handleStoreChange}
          />
          {errors?.store_id?.map((message, index) => (
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
      </form>
    </VStack>
  );
};
