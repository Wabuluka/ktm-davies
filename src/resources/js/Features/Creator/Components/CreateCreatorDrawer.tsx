import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useRef, useState } from 'react';
import { useCreateCreatorMutation } from '../Hooks/useCreateCreatorMutation';
import { CreatorFormData } from '../Types';
import { CreatorForm } from './CreatorForm';

type Props = {
  isOpen: boolean;
  onClose: () => void;
  onStoreSuccess?: (creator: CreatorFormData) => void;
};

export function CreateCreatorDrawer({
  isOpen,
  onClose,
  onStoreSuccess,
}: Props) {
  const formId = useId();
  const firstInput = useRef(null);
  const mutation = useCreateCreatorMutation();
  const [errors, setErrors] = useState<Record<string, string[]>>();

  function handleClose() {
    setErrors(undefined);
    onClose();
  }
  function handleSubmit(formData: CreatorFormData) {
    mutation.mutate(
      { ...formData },
      {
        onSuccess: () => {
          onStoreSuccess?.(formData);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      },
    );
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose} initialFocusRef={firstInput}>
      <Text>Edit Creator</Text>
      <CreatorForm
        id={formId}
        errors={errors}
        initialFocusRef={firstInput}
        onSubmit={handleSubmit}
      />
      <ButtonGroup>
        <Button onClick={handleClose}>Back</Button>
        <PrimaryButton
          type="submit"
          form={formId}
          isLoading={mutation.isLoading}
        >
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
