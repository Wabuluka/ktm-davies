import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { useShowLabelQuery } from '@/Features/Label/Hooks/useShowLabelQuery';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { ComponentProps, FC, useRef, useState } from 'react';
import { useQueryClient } from 'react-query';
import { useDeleteLabelMutation } from '../Hooks/useDeleteLabelMutation';
import { useEditLabelMutation } from '../Hooks/useEditLabelMutation';
import { Form } from './Form';

type Props = {
  labelId: number;
  onLabelDeleted?: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const EditLabelDrawer: FC<Props> = ({
  labelId,
  onLabelDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [errors, setErrors] = useState<Record<string, string[]>>();
  const editLabelMutation = useEditLabelMutation();
  const deleteLabelMutation = useDeleteLabelMutation();
  const labelQueryKeys = useQueryKeys().label;
  const labelIndexQueryKey = labelQueryKeys.all;
  const labelShowQueryKey = labelQueryKeys.show(labelId);
  const {
    data: label,
    isLoading,
    isError,
  } = useShowLabelQuery(labelId, {
    enabled: isOpen,
  });
  const queryClient = useQueryClient();

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleDelete = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
    e.preventDefault();

    if (!label) return;

    if (window.confirm(`Are you sure to delete ${label.name}?`)) {
      deleteLabelMutation.mutate(labelId, {
        onSuccess: () => {
          queryClient.invalidateQueries(labelIndexQueryKey);
          queryClient.removeQueries(labelShowQueryKey);
          onLabelDeleted && onLabelDeleted(labelId);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
    }
  };

  const handleSubmit: ComponentProps<typeof Form>['onSubmit'] = (e, label) => {
    e.preventDefault();
    e.stopPropagation();

    editLabelMutation.mutate(
      { id: labelId, ...label },
      {
        onSuccess: () => {
          queryClient.invalidateQueries(labelIndexQueryKey);
          queryClient.invalidateQueries(labelShowQueryKey);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      },
    );
  };

  const firstInput = useRef(null);

  return (
    <>
      {renderOpenDrawerElement(onOpen)}
      <Drawer
        isOpen={isOpen}
        onClose={handleClose}
        initialFocusRef={firstInput}
      >
        <Text>Edit Label</Text>
        {isLoading ? (
          <LoadingSpinner />
        ) : isError || !label ? (
          <DataFetchError />
        ) : (
          <Form
            id="label-form"
            label={label}
            errors={errors}
            onSubmit={handleSubmit}
            initialFocusRef={firstInput}
          />
        )}
        <ButtonGroup>
          <Button variant="outline" onClick={handleClose}>
            Back
          </Button>
          <DangerButton
            onClick={handleDelete}
            isLoading={deleteLabelMutation.isLoading}
          >
            Delete
          </DangerButton>
          <PrimaryButton type="submit" form="label-form" isDisabled={isLoading}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
