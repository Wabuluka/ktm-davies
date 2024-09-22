import {
  RelatedItemFormData,
  RelatedItemOnBookForm,
} from '@/Features/RelatedItem';
import { uniqueId } from 'lodash';
import {
  PropsWithChildren,
  createContext,
  useContext,
  useReducer,
} from 'react';
import { BookFormData } from '../../Book/Types';

type State = BookFormData['related_items'];

type Action =
  | {
      type: 'set';
      newState: State;
    }
  | {
      type: 'add';
      relatedItem: RelatedItemFormData;
    }
  | {
      type: 'update';
      relatedItem: RelatedItemFormData;
      id: RelatedItemOnBookForm['id'];
    }
  | {
      type: 'move-up';
      relatedItem: RelatedItemOnBookForm;
    }
  | {
      type: 'move-down';
      relatedItem: RelatedItemOnBookForm;
    }
  | {
      type: 'delete';
      id: RelatedItemOnBookForm['id'];
    };

export const RelatedItemsContext = createContext<State | null>(null);

export const DispatchRelatedItemsContext = createContext<
  React.Dispatch<Action> | undefined
>(undefined);

export function useRelatedItems() {
  return useContext(RelatedItemsContext);
}

export function useDispatchRelatedItems() {
  return useContext(DispatchRelatedItemsContext);
}

function reducer(state: State, action: Action): State {
  switch (action.type) {
    case 'add':
      return {
        ...state,
        upsert: [
          ...state.upsert,
          {
            ...action.relatedItem,
            id: uniqueId('+'),
            sort: state.upsert.length + 1,
          },
        ],
      };
    case 'set':
      return action.newState;
    case 'update':
      return {
        ...state,
        upsert: state.upsert.map((item) =>
          item.id === action.id ? { ...item, ...action.relatedItem } : item,
        ),
      };
    case 'move-up':
      if (action.relatedItem.sort < 2) {
        throw new Error('The sorting order is invalid');
      }
      return {
        ...state,
        upsert: state.upsert
          .map((item) => {
            if (action.relatedItem.sort === item.sort) {
              return { ...item, sort: item.sort - 1 };
            }
            if (action.relatedItem.sort - 1 === item.sort) {
              return { ...item, sort: item.sort + 1 };
            }
            return item;
          })
          .sort((a, b) => a.sort - b.sort),
      };
    case 'move-down':
      if (action.relatedItem.sort >= state.upsert.length) {
        throw new Error('The sorting order is invalid.');
      }
      return {
        ...state,
        upsert: state.upsert
          .map((item) => {
            if (action.relatedItem.sort === item.sort) {
              return { ...item, sort: item.sort + 1 };
            }
            if (action.relatedItem.sort + 1 === item.sort) {
              return { ...item, sort: item.sort - 1 };
            }
            return item;
          })
          .sort((a, b) => a.sort - b.sort),
      };
    case 'delete':
      return {
        upsert: state.upsert
          .filter((item) => item.id !== action.id)
          .map((item, index) => ({ ...item, sort: index + 1 })),
        deleteIds: action.id.startsWith('+')
          ? state.deleteIds
          : [...state.deleteIds, action.id],
      };
    default:
      return state;
  }
}

export function RelatedItemsProvider({
  initialState = { upsert: [], deleteIds: [] },
  children,
}: PropsWithChildren<{ initialState?: State }>) {
  const [relatedItems, dispatch] = useReducer(reducer, initialState);

  return (
    <RelatedItemsContext.Provider value={relatedItems}>
      <DispatchRelatedItemsContext.Provider value={dispatch}>
        {children}
      </DispatchRelatedItemsContext.Provider>
    </RelatedItemsContext.Provider>
  );
}
