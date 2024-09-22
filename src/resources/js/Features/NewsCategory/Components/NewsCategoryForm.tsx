import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import {
  ButtonGroup,
  FormControl,
  FormErrorMessage,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps } from 'react';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { UseNewsCategoryFormReturn } from '@/Features/NewsCategory/Hooks/useNewsCategoryForm';
import { UseDestroyNewsCategoryFormReturn } from '@/Features/NewsCategory/Hooks/useDestroyNewsCategoryForm';

type Props = {
  data: UseNewsCategoryFormReturn['data'];
  errors: UseNewsCategoryFormReturn['errors'];
  setData: UseNewsCategoryFormReturn['setData'];
  processing: UseNewsCategoryFormReturn['processing'];
  destroy?: {
    handler: () => void;
    errors: UseDestroyNewsCategoryFormReturn['errors'];
    processing: boolean;
  };
} & Omit<ComponentProps<'form'>, 'children'>;

export function NewsCategoryForm({
  data,
  errors,
  setData,
  processing,
  destroy,
  ...props
}: Props) {
  const isInvalidName = !!errors.name || !!destroy?.errors.name;
  function handleNameChange(e: React.ChangeEvent<HTMLInputElement>) {
    setData('name', e.target.value);
  }
  function handleDestroy() {
    if (confirm('本当に削除しますか？')) {
      destroy?.handler();
    }
  }

  return (
    <form {...props}>
      <VStack spacing={12}>
        <FormControl isInvalid={isInvalidName} isRequired>
          <FormLabel>Category Name</FormLabel>
          <Input
            type="text"
            value={data.name}
            onChange={handleNameChange}
            maxLength={255}
            fontSize="lg"
            fontWeight="semibold"
            px={4}
            py={8}
          />
          <FormErrorMessage>{errors.name}</FormErrorMessage>
          <FormErrorMessage>{destroy?.errors.name}</FormErrorMessage>
        </FormControl>
        <ButtonGroup
          spacing={4}
          mr="auto"
          isDisabled={processing || destroy?.processing}
        >
          {!!destroy && (
            <DangerButton
              type="button"
              onClick={handleDestroy}
              isLoading={destroy.processing}
            >
              Delete
            </DangerButton>
          )}
          <PrimaryButton type="submit" isLoading={processing}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </VStack>
    </form>
  );
}
