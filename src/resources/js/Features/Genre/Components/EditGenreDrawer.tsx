import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import { isLaravelValidationError } from '@/Features/Misc/Api/Utils/isLaravelValidationError';
import { useShowGenreQuery } from '@/Features/Genre/Hooks/useShowGenreQuery';
import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useDisclosure } from '@chakra-ui/react';
import { ComponentProps, FC, useRef, useState } from 'react';
import { useQueryClient } from 'react-query';
import { useDeleteGenreMutation } from '../Hooks/useDeleteGenreMutation';
import { useEditGenreMutation } from '../Hooks/useEditGenreMutation';
import { Form } from './Form';

type Props = {
  genreId: number;
  onGenreDeleted?: (id: number) => void;
  renderOpenDrawerElement: (onOpen: () => void) => JSX.Element;
};

export const EditGenreDrawer: FC<Props> = ({
  genreId,
  onGenreDeleted,
  renderOpenDrawerElement,
}) => {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [errors, setErrors] = useState<Record<string, string[]>>();
  const editGenreMutation = useEditGenreMutation();
  const deleteGenreMutation = useDeleteGenreMutation();
  const genreQueryKeys = useQueryKeys().genre;
  const genreIndexQueryKey = genreQueryKeys.all;
  const genreShowQueryKey = genreQueryKeys.show(genreId);
  const {
    data: genre,
    isLoading,
    isError,
  } = useShowGenreQuery(genreId, {
    enabled: isOpen,
  });
  const queryClient = useQueryClient();

  const handleClose = () => {
    setErrors(undefined);
    onClose();
  };

  const handleDelete = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
    e.preventDefault();

    if (!genre) return;

    if (window.confirm(`Are you sure to delete ${genre.name}?`)) {
      deleteGenreMutation.mutate(genreId, {
        onSuccess: () => {
          queryClient.invalidateQueries(genreIndexQueryKey);
          queryClient.removeQueries(genreShowQueryKey);
          onGenreDeleted && onGenreDeleted(genreId);
          handleClose();
        },
        onError: (error) => {
          isLaravelValidationError(error) &&
            setErrors(error?.response?.data?.errors);
        },
      });
    }
  };

  const handleSubmit: ComponentProps<typeof Form>['onSubmit'] = (e, genre) => {
    e.preventDefault();
    e.stopPropagation();

    editGenreMutation.mutate(
      { id: genreId, ...genre },
      {
        onSuccess: () => {
          queryClient.invalidateQueries(genreIndexQueryKey);
          queryClient.invalidateQueries(genreShowQueryKey);
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
        <Text>Edit Genre</Text>
        {isLoading ? (
          <LoadingSpinner />
        ) : isError || !genre ? (
          <DataFetchError />
        ) : (
          <Form
            id="genre-form"
            initialValues={genre}
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
            isLoading={deleteGenreMutation.isLoading}
          >
            Delete
          </DangerButton>
          <PrimaryButton type="submit" form="genre-form" isDisabled={isLoading}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
    </>
  );
};
