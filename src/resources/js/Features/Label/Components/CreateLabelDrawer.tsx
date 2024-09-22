import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { FC, useRef, useState } from 'react';
import { QueryKey, useQueryClient } from 'react-query';
import { useCreateLabelMutation } from '../Hooks/useCreateLabelMutation';
import { Form, formValues } from './Form';

type Props = {
  queryKey: QueryKey;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const CreateLabelDrawer: FC<Props> = ({
  queryKey,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [errors, setErrors] = useState<Record<string, string[]>>();
  const createLabelMutation = useCreateLabelMutation();
  const firstInput = useRef(null);
  const queryClient = useQueryClient();

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleSubmit = (
    e: React.FormEvent<HTMLFormElement>,
    label: formValues,
  ) => {
    e.preventDefault();
    e.stopPropagation();
    createLabelMutation.mutate(label, {
      onSuccess: () => {
        queryClient.invalidateQueries(queryKey);
        handleClose();
      },
      onError: (error) => {
        isLaravelValidationError(error) &&
          setErrors(error?.response?.data?.errors);
      },
    });
  };

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer
        isOpen={isOpen}
        onClose={handleClose}
        initialFocusRef={firstInput}
      >
        <Text>Create Label</Text>
        <Form
          id="label-form"
          errors={errors}
          onSubmit={handleSubmit}
          initialFocusRef={firstInput}
        />
        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <PrimaryButton
            type="submit"
            isLoading={createLabelMutation.isLoading}
            form="label-form"
          >
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
