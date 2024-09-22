import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useRef, useState } from 'react';
import { useCreateCreationTypeMutation } from '../Hooks/useCreateCreationTypeMutation';
import { CreationTypeFormData } from '../Types';
import { CreationTypeForm } from './CreationTypeForm';

type Props = {
  isOpen: boolean;
  onClose: () => void;
};

export function CreateCreationTypeDrawer({ isOpen, onClose }: Props) {
  const formId = useId();
  const firstInput = useRef(null);
  const mutation = useCreateCreationTypeMutation();
  const [errors, setErrors] = useState<Record<string, string[]>>();

  function handleClose() {
    setErrors(undefined);
    onClose();
  }
  function handleSubmit(formData: CreationTypeFormData) {
    mutation.mutate(
      { ...formData },
      {
        onSuccess: handleClose,
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      },
    );
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose} initialFocusRef={firstInput}>
      <Text>Create Creation Type</Text>
      <CreationTypeForm
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
