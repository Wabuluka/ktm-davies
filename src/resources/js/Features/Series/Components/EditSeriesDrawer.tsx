import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { useShowSeriesQuery } from '@/Features/Series/Hooks/useShowSeriesQuery';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { ComponentProps, FC, useRef, useState } from 'react';
import { useQueryClient } from 'react-query';
import { useDeleteSeriesMutation } from '../Hooks/useDeleteSeriesMutation';
import { useEditSeriesMutation } from '../Hooks/useEditSeriesMutation';
import { Form } from './Form';

type Props = {
  seriesId: number;
  onSeriesDeleted?: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const EditSeriesDrawer: FC<Props> = ({
  seriesId,
  onSeriesDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [errors, setErrors] = useState<Record<string, string[]>>();
  const editSeriesMutation = useEditSeriesMutation();
  const deleteSeriesMutation = useDeleteSeriesMutation();
  const seriesQueryKeys = useQueryKeys().series;
  const seriesIndexQueryKey = seriesQueryKeys.all;
  const seriesShowQueryKey = seriesQueryKeys.show(seriesId);
  const {
    data: series,
    isLoading,
    isError,
  } = useShowSeriesQuery(seriesId, {
    enabled: isOpen,
  });
  const queryClient = useQueryClient();

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleDelete = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
    e.preventDefault();

    if (!series) return;

    if (window.confirm(`Are you sure to delete ${series.name}？`)) {
      deleteSeriesMutation.mutate(seriesId, {
        onSuccess: () => {
          queryClient.invalidateQueries(seriesIndexQueryKey);
          queryClient.removeQueries(seriesShowQueryKey);
          onSeriesDeleted && onSeriesDeleted(seriesId);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
    }
  };

  const handleSubmit: ComponentProps<typeof Form>['onSubmit'] = (e, series) => {
    e.preventDefault();
    e.stopPropagation();

    editSeriesMutation.mutate(
      { id: seriesId, ...series },
      {
        onSuccess: () => {
          queryClient.invalidateQueries(seriesIndexQueryKey);
          queryClient.invalidateQueries(seriesShowQueryKey);
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
        <Text>Edit Series</Text>
        {isLoading ? (
          <LoadingSpinner />
        ) : isError || !series ? (
          <DataFetchError />
        ) : (
          <Form
            id="series-form"
            initialValues={series}
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
            isLoading={deleteSeriesMutation.isLoading}
          >
            Delete
          </DangerButton>
          <PrimaryButton
            type="submit"
            form="series-form"
            isDisabled={isLoading}
          >
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
